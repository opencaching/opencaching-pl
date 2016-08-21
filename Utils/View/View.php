<?php
namespace Utils\View;

class View {

    private $forbiddenVarNames = array(
      "forbiddenVarNames"
    );

    private $loadJQuery = false;
    private $loadBootstrap = false;

    /**
     * Return true if view can't contains variable with given name
     * @param unknown $name
     */
    private function isNameForbidden($name){
        if(in_array($name, $this->forbiddenVarNames))
            return true;
        else
            return false;
    }

    /**
     * Set given variable as local View variable
     * (inside template only View variables are accessible)
     *
     * @param String $varName
     * @param  $varValue
     */
    public function setVar($varName, $varValue){
        if($this->isNameForbidden($varName)){
            $this->error("Can't set View variable with name: ".$varName);
            return;
        }

        $this->$varName = $varValue;
    }

    public function loadJQuery(){
        $this->loadJQuery = true;
    }

    public function loadBootstrap(){
        $this->loadJQuery = true;
        $this->loadBootstrap = true;
    }

    public function shouldLoadJquery(){
        return $this->loadJQuery;
    }

    public function shouldLoadBootstrap(){
        return $this->loadBootstrap;
    }


    private function error($message){
        error_log($message);
    }

}