<?php

namespace Oh86\GW\Auth\Guard;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class RequestGuard implements Guard
{
    protected Request $request;

    /**
     * @var null|User
     */
    protected $user = null;

    protected string $header;
    protected string $userClass;

    /**
     * @param \Illuminate\Http\Request $request
     * @param array{header: string, user-class: string} $config
     */
    public function __construct(Request $request, array $config)
    {
        $this->request = $request;
        $this->header = $config['header'];
        $this->userClass = $config['user-class'];
    }

    public function check()
    {
        return (bool) $this->user();
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $str = $this->request->header($this->header);
        if (!$str) {
            return null;
        }

        $data = json_decode($str, true, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!$data) {
            return null;
        }

        $this->user = new $this->userClass;
        $this->user->setData($data);

        return $this->user;
    }

    public function id()
    {
        return $this->user()->getAuthIdentifier();
    }

    public function validate(array $credentials = [])
    {
        return true;
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getUserClass()
    {
        return $this->userClass;
    }
}