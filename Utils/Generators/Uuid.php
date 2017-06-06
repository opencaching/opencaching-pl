<?php
namespace Utils\Generators;


class Uuid
{
    /**
     * -- This script is moved here from clicompatbase
     *
     * Create a "universal unique" replication "identifier"
     */
    public static function create()
    {
        $uuid = mb_strtoupper(md5(uniqid(rand(), true)));

        // split into XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
        // (type VARCHAR 36, case insensitiv)
        $uuid = mb_substr($uuid, 0, 8) . '-' . mb_substr($uuid, -24);
        $uuid = mb_substr($uuid, 0, 13) . '-' . mb_substr($uuid, -20);
        $uuid = mb_substr($uuid, 0, 18) . '-' . mb_substr($uuid, -16);
        $uuid = mb_substr($uuid, 0, 23) . '-' . mb_substr($uuid, -12);

        return $uuid;
    }

}

