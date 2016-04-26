<?php
/**
 * This file contains configuration defaults for OC module 'Example'
 *
 *
 * Please note:
 *      DON'T EDIT THIS FILE to override default values for your server.
 *
 *      Instead of that locate ExampleLocal class in file ExampleLocal.php
 *      and there add your overrides.
 */

namespace Config;

class XExampleDefaults
{
    /**
     * Here we define default value of simple var exampleVar1
     */
    public function getXExampleVar1(){
        return 'Xexample1';
    }

    /**
     * Here we define default value of simple var exampleVar2
     */
    public function getXExampleVar2(){
        return 'Xexample2';
    }

    /**
     * Here we define default value of array var exampleVar2
     */
    public function getXExampleVar3(){
        return array(
                    'Xkey31'  => 'Xvalue31',
                    'Xkey32'  => 'Xvalue32'
                );
    }

}