<?php

/**
 * menu for power Trails
 */
class powerTrailMenu
{
    private array $menu = [];

    public function __construct($user)
    {
        if ($user) {
            $this->menu = [
                1 => [
                    'name' => tr('pt012'),
                    'action' => 'showAllSeries',
                    'script' => 'powerTrail.php',
                ],
                2 => [
                    'name' => tr('pt003'),
                    'action' => 'createNewSerie',
                    'script' => 'powerTrail.php',
                ],
                3 => [
                    'name' => tr('pt013'),
                    'action' => 'selectCaches',
                    'script' => 'powerTrail.php',
                ],
                4 => [
                    'name' => tr('pt062'),
                    'action' => 'mySeries',
                    'script' => 'powerTrail.php',
                ],

            ];
        }
    }

    public function getPowerTrailsMenu(): array
    {
        return $this->menu;
    }
}
