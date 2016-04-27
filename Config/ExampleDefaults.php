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

class ExampleDefaults
{
    /**
     * Here we define default value of simple var exampleVar1
     */
    public function getExampleVar1(){
        return 'example1';
    }

    /**
     * Here we define default value of simple var exampleVar2
     */
    public function getExampleVar2(){
        return 'example2';
    }

    /**
     * Here we define default value of array var exampleVar2
     */
    public function getExampleVar3(){
        return array(
                    'key31'  => 'value31',
                    'key32'  => 'value32'
                );
    }

}