<?php
namespace lib\Objects\User\UserPreferences;


class TestUserPref extends UserPreferencesBaseData
{
    const KEY = 'bubo';

    public function __construct($key)
    {
        parent::__construct($key);
    }

    public function getDefaults()
    {
        return [
            'fooVar' =>'defaultFoo',
            'booVar' => 'defaultBoo',
        ];
    }


}

