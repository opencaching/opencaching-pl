<?php

/**
 * menu for power Trails
 */
class powerTrailMenu
{
    private $menu;

    function __construct($user) {

        if ($user) {
            $this->menu = array (
                1 => array (
                    'name' => tr('pt012'),
                    'action'=> 'showAllSeries',
                    'script' => 'powerTrail.php',
                ),
                2 => array (
                    'name' => tr('pt003'),
                    'action'=> 'createNewSerie',
                    'script' => 'powerTrail.php',
                ),
                3 => array (
                    'name' => tr('pt013'),
                    'action'=> 'selectCaches',
                    'script' => 'powerTrail.php',
                ),
                4 => array (
                    'name' => tr('pt062'),
                    'action'=> 'mySeries',
                    'script' => 'powerTrail.php',
                ),

            );
        } else $this->menu = array ();
    }

    public function getPowerTrailsMenu()
    {
        return $this->menu;
    }
}
