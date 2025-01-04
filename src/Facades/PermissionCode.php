<?php

namespace Oh86\GW\Auth\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Oh86\GW\Auth\Permission\PermissionCode
 * @method static setCodes(string[] $codes)
 * @method static string[] getCodes()
 */
class PermissionCode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Oh86\GW\Auth\Permission\PermissionCode::class;
    }
}