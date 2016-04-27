<?php
/**
 * This file contains configuration overrides for OC module 'Example' specific for OCPL site
 *
 * Please note:
 *      DON'T EDIT THIS FILE to override default values for your server.
 *
 *      Instead of that locate ExampleLocal class in file ExampleLocal.php
 *      and there add your local overrides.
 *
 *      If you want to change this file deliver changes to repo.
 */

namespace Config;

class XExampleDefaultsRO extends XExampleDefaults
{
    /**
     * Here we define OCPL specific override of default value of simple var exampleVar1
     */
    public function getXExampleVar1(){
        return 'Xexample1-RO-override';
    }

    /**
     * For the rest of config vars in this module OCPL uses default values from class ExampleDefaults
     */

}

