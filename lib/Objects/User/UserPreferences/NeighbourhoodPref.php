<?php
namespace lib\Objects\User\UserPreferences;

use lib\Objects\Neighbourhood\Neighbourhood;

class NeighbourhoodPref extends UserPreferencesBaseData
{
    const KEY = 'MyNSettings';

    public function getDefaults()
    {
        return [
            'style' => [ 'name' => 'full', 'caches-count' => 5],
            'items' => [ 
                Neighbourhood::ITEM_LATESTCACHES => [ 'order' => 1, 'show' => 1, 'fullsize' => 0],
                Neighbourhood::ITEM_UPCOMINGEVENTS => [ 'order' => 4, 'show' => 1, 'fullsize' => 0],
                Neighbourhood::ITEM_MAP => [ 'order' => 3, 'show' => 1, 'fullsize' => 1],
                Neighbourhood::ITEM_LATESTLOGS => [ 'order' => 2, 'show' => 1, 'fullsize' => 0],
                Neighbourhood::ITEM_FTFCACHES => [ 'order' => 5, 'show' => 1, 'fullsize' => 0],
                Neighbourhood::ITEM_TITLEDCACHES => [ 'order' => 6, 'show' => 1, 'fullsize' => 0],
                Neighbourhood::ITEM_RECOMMENDEDCACHES => [ 'order' => 7, 'show' => 1, 'fullsize' => 0],
            ]
        ];
    }
}