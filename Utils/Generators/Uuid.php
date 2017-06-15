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

    /**
     * Returns the sql chunk which generates uppercase unique UUID
     * in format XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
     * It can be used for example as:
     * UPDATE table SET uuidColum = Uuid::sqlUppercaseUuid()
     */
    public static function getSqlForUpperCaseUuid()
    {
        // md5 because uuid() returns "similar" results if generates in really short time - with md5 uuids are much different
        return "(SELECT CONCAT(SUBSTR(( @_u:= UPPER( md5( UUID() ) ) ),1,8),'-',SUBSTR(@_u,9,4),'-',SUBSTR(@_u,13,4),'-',SUBSTR(@_u,17,4),'-',SUBSTR(@_u,21,12)))";
    }

}

