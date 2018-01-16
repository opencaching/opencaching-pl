<?php

namespace lib\Objects\CacheSet;

use lib\Objects\BaseObject;
use lib\Objects\User\User;

class CacheSetOwner extends BaseObject
{
    const ROLE_AUTHOR = 1;
    const ROLE_ADMIN = 2;
    const ROLE_MENTOR = 3;

    private $userId;
    private $username;
    private $role;

    public function __construct()
    {
        parent::__construct();
    }


    public function getUserId()
    {
        return $this->userId;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setUserId($val)
    {
        $this->userId = $val;
        return $this;
    }

    public function setUserName($val)
    {
        $this->username = $val;
        return $this;
    }

    public function setRole($val)
    {
        $this->role = $val;
        return $this;
    }

    public function getUserProfileUrl()
    {
        return User::GetUserProfileUrl($this->userId);
    }

    /**
     * Get owners (CacheSetOwners) lists for many cacheSets (by cacheSet Ids)
     *
     * @param array $cacheSetIds
     * @return array|\lib\Objects\CacheSet\CacheSetOwner
     */
    public static function getOwnersOfCacheSets(array $cacheSetIds)
    {
        $db = self::db();

        if( empty($cacheSetIds) ){
            return [];
        }
        
        $csIdsStr = implode(',', $cacheSetIds);

        $stmt = $db->simpleQuery(
            "SELECT pto.PowerTrailId AS ptId, pto.privileages as role,
                u.user_id AS uId, username AS name
            FROM PowerTrail_owners AS pto
                LEFT JOIN user AS u ON u.user_id = pto.userId
            WHERE PowerTrailId IN ($csIdsStr)");

        $result = array_fill_keys($cacheSetIds, []);
        while($row = $db->dbResultFetch($stmt)){
            $cso = new self();
            $cso->setUserId($row['uId'])
                ->setUserName($row['name'])
                ->setRole($row['role']);

            $result[$row['ptId']][] = $cso;
        }
        return $result;
    }

    /**
     * Set owners array to many cacheSets objects in one query
     *
     * @param array $cacheSets
     * @return array
     */
    public static function setOwnersToCacheSets(array $cacheSets)
    {
        $csIds = [];
        foreach ($cacheSets as $cs){
            $csIds[] = $cs->getId();
        }

        $owners = self::getOwnersOfCacheSets($csIds);

        foreach ($cacheSets as $cs){
            $cs->setOwners( $owners[$cs->getId()] );
        }

        return $cacheSets;
    }

}



