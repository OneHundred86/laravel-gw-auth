<?php

namespace Oh86\GW\Auth\Permission;

class PermissionCode
{
    /** @var string[] */
    private $codes = [];

    /**
     * @param string[] $codes
     */
    public function setCodes($codes)
    {
        $this->codes = $codes;
    }

    public function getCodes()
    {
        return $this->codes;
    }

    public function hasCode(string $code)
    {
        return in_array($code, $this->codes);
    }
}