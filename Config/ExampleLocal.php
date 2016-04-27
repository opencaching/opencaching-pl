<?php
/**
 * This file contains local configuration overrides for OC module 'Example'
 *
 * Any changes to this file shouldn't be commited to the repository.
 *
 * Please note:
 *      This class can extend:
 *          - module global defaults from class: <module-name>Defaults
 *          - or optionally node-defaults from class: <module-name>Defaults<countryId>
 *
 *      To access config variables use this class.
 */

namespace Config;

final class ExampleLocal extends ExampleDefaultsPL
{
    /*
     * Remove this comment and add your overrides here based on variables from parent classes.
     * Every override should be in format of php class public static getter which returns requested value.
     *
     * DON'T COMMIT ANY CHANGES TO THIS FILE to OC repository.
     */

    /**
     * Here is a local override of default value of simple var exampleVar2
     */
    public function getExampleVar2(){
        return 'example2p-local-overrdie';
    }

}