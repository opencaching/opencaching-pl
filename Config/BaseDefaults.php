<?php
/**
 * This file contains configuration base for OC configuration ( meta connfiguration :) )
 *
 * This file can't have node-specific version (like BaseDefaultsPL or something like that)!
 *
 * Please note:
 *      DON'T EDIT THIS FILE to override default values for your server.
 *
 *      Instead of that locate ExampleLocal class in file ExampleLocal.php
 *      and there add your overrides.
 */

namespace Config;

class BaseDefaults
{
    /**
     *
     * By deafult any node is not enables (generic configuration is served).
     * If you want load specific node configuration override this value in BaseLocal class/file
     *
     * @return the oc nodeId string or empty string if any node config is not requested
     */
    public function getConfigNodeId(){
        return '';
    }

    /**
     * Does configuration from 'old-style-config' should be returns by config classes?
     * By default use 'old-style-config' values (from file settings.inc.php).
     *
     * If you want use "new-configuration" stored in functions in /Config dir
     * override this value in BaseLocal class/file
     *
     */
    public function useNewConfig(){
        return false;
    }

}