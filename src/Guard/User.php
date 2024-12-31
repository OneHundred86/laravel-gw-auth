<?php

namespace Oh86\GW\Auth\Guard;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;

class User implements Authenticatable, Arrayable, \JsonSerializable
{
    protected string $idName = 'id';

    private array $data;

    public function __construct()
    {
    }

    public function getAuthIdentifierName()
    {
        return $this->idName;
    }

    public function getAuthIdentifier()
    {
        return $this->data[$this->idName];
    }

    public function getAuthPassword()
    {
        return "";
    }

    public function getRememberToken()
    {
        return "";
    }

    public function setRememberToken($value)
    {

    }

    public function getRememberTokenName()
    {
        return "";
    }

    public function toArray()
    {
        return $this->data;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}