<?php

namespace lib\Objects\User\UserPreferences;



abstract class UserPreferencesBaseData {

    private $key;
    private $values;


    /**
     * This function should be implemented in child-class
     * @return array of default values
     *
     *
     * Example of implementation:
     *   return [ show_ignored='false', hide_submenu='true' ];
     *
     */
    public abstract function getDefaults();

    /**
     * @param unknown $key - key must be one of ALLOWED_KEYS value from class UserPreferences
     *    Every implementation of UserPreferencesData should has its own key!
     */
    public function __construct($key){

        if(UserPreferences::isKeyAllowed($key)){
            $this->key = $key;
        }else{
            $this->key = null;
        }

        $this->values = [];
    }

    public final function getKey(){
        return $this->key;
    }

    public function getValues(){
        return $this->values;
    }

    public function getJsonValues(){
        return json_encode($this->values);
    }

    public function setValues($values){
        foreach ($this->getDefaults() as $key=>$default){
            if(array_key_exists($key, $values)){
                $this->values[$key] = $values[$key];
            }else{
                $this->values[$key] = $default;
            }
        }
    }

    public function setJsonValues($jsonValues){
        $this->setValues(json_decode($jsonValues, true));
    }

    public function loadDefaults(){
        $this->values = $this->getDefaults();
    }

}

