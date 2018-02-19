<?php

namespace lib\Objects\User\UserPreferences;

use lib\Objects\BaseObject;
use Utils\Debug\Debug;

/**
 * This class is used to manage user preferences at oc pages
 * (for example map filter settings) between user sessions.
 *
 * This class allow to easy maintain user prefs. - all preferences is stored as a JSON text
 * If there is no previus setting for some key defaults values are returned.
 *
 * UserPreferences data is stored in DB table user_preferences.
 *
 */
class UserPreferences extends BaseObject
{

    /**
     * This const array should contain all keys defined anywhere in the code in format:
     * <key-name-in-db> => <class-derrived-from-UserPreferencesBaseData>
     */
    const ALLOWED_KEYS = [
        // 'testKey' => 'TestData::class'
        // TestUserPref::KEY => TestUserPref::class,
        UserProfilePref::KEY => UserProfilePref::class
    ];

    /** @var UserPreferencesBaseData[] */
    private $dataObjects = []; //UserPreferencesBaseData objects loaded from DB


    /**
     * Returns UserPreferencesBaseData by given key. DB data are merged with defaults.
     *
     * @param string $key
     * @return UserPreferencesBaseData object or null on failure
     */
    public static function getUserPrefsByKey($key){
        return self::GetUserPrefsByKeys([$key])->getDataObject($key);
    }

    /**
     * Returns UserPreferences object which contains many UserPreferencesBaseData
     * objects loaded by given keys array.
     *
     * UserPreferencesBaseData objects can be accessed by getDataObject method.
     *
     * @param string[] $keys
     * @return UserPreferences
     */
    public static function getUserPrefsByKeys(array $keys){

        if(empty($keys)){
            return null;
        }

        // check keys
        foreach ($keys as $key){
            if(!self::isKeyAllowed($key)){
                Debug::errorLog("Unknown UserPreferences key = $key");
                return null;
            }
        }

        //create instance and load keys
        $obj = new self();
        $obj->loadByKeys($keys);
        return $obj;
    }

    /**
     * Save given json with user-preferences under given key in DB.
     * Values from json are merged with defaults for given key.
     *
     * @return true on success
     */
    public static function savePreferencesJson($key, $json){

        if( !self::isKeyAllowed($key) ){
            Debug::errorLog(__METHOD__.": Unknown key $key");
            return false;
        }

        $prefsObj = self::getUserPrefObjForKey($key);
        $prefsObj->setJsonValues($json);

        // find current userId
        $user = self::getCurrentUser();
        if(is_null($user)){
            // user not logged!?
            return false;
        }
        $userId = $user->getUserId();

        return false !== self::db()->multiVariableQuery(
            "INSERT INTO user_preferences (user_id, `key`, value)
             VALUES (:1, :2, :3)
                ON DUPLICATE KEY UPDATE value = VALUES(value)",
            $userId, $key, $prefsObj->getJsonValues());

    }


    /**
     * Returns true if this key is allowed (defined in ALLOWED_KEYS)
     */
    public static function isKeyAllowed($key){
        return array_key_exists($key, self::ALLOWED_KEYS);
    }

    /**
     * Returns proper UserPreferencesBaseData class based on ALLOWED_KEYS definition
     * for given key.
     *
     * @param string $key
     * @return UserPreferencesBaseData object
     */
    private static function getUserPrefObjForKey($key){

        if ( isset(self::ALLOWED_KEYS[$key]) ) {
            $className = self::ALLOWED_KEYS[$key];
            return new $className($key);
        }else{
            Debug::errorLog(__METHOD__.": Unknown class for key: $key");
            return null;
        }
    }

    public function __construct(){
        parent::__construct();
    }

    private function loadByKeys($keys){

        if(empty($keys)){
            return;
        }

        /** @var User */
        $user = self::getCurrentUser();
        if(is_null($user)){
            return;
        }

        $userId = $user->getUserId();

        $db = self::db();

        $quotedKeys = [];
        foreach($keys as $k){
            $quotedKeys[] = $db->quote($k);
        }
        $keysStr = implode(',', $quotedKeys);

        $stmt = $db->multiVariableQuery(
            "SELECT * FROM user_preferences
             WHERE user_id = :1 AND `key` IN ( $keysStr )", $userId);

        while($row = $db->dbResultFetch($stmt)){
            $key = $row['key'];
            $obj = self::getUserPrefObjForKey($key);
            $obj->setJsonValues($row['value']);
            $this->dataObjects[$key] = $obj;
        }

        // add keys not found in DB with default values
        foreach ($keys as $key){
            if(!array_key_exists($key, $this->dataObjects)){
                $obj = self::getUserPrefObjForKey($key);
                $obj->loadDefaults();
                $this->dataObjects[$key] = $obj;
            }
        }
    }

    public function getDataObject($key){
        return (array_key_exists($key, $this->dataObjects)) ? $this->dataObjects[$key] : null;
    }
}
