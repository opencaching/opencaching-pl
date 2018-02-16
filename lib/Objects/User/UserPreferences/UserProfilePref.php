<?php
namespace lib\Objects\User\UserPreferences;

class UserProfilePref extends UserPreferencesBaseData
{
    const KEY = 'UserProfile';
    
    public function getDefaults()
    {
        return [
            'email' => [
                'showMyEmail' => false,
                'recieveCopy' => false
            ]
        ];
    }
}