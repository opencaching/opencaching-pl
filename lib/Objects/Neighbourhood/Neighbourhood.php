<?php
namespace lib\Objects\Neighbourhood;

use lib\Objects\BaseObject;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\User\User;
use lib\Controllers\Php7Handler;

class Neighbourhood extends BaseObject
{

    const ITEM_MAP = 1;
    const ITEM_LATESTCACHES = 2;
    const ITEM_UPCOMINGEVENTS = 3;
    const ITEM_FTFCACHES = 4;
    const ITEM_LATESTLOGS = 5;
    const ITEM_TITLEDCACHES = 6;
    const ITEM_RECOMMENDEDCACHES = 7;

    /**
     * Id in DB
     *
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $userid;

    /**
     * User who neighbourhood belongs to
     *
     * @var User
     */
    private $user = null;

    /**
     * Number in neighbourhoods sequence
     *
     * @var integer
     */
    private $seq;

    /**
     * Name of neighbourhood
     *
     * @var string
     */
    private $name;

    /**
     * Coords of neighbourhood's center
     *
     * @var Coordinates
     */
    private $coords;

    /**
     * Radius of neighbourhood
     *
     * @var int
     */
    private $radius;

    /**
     * User will receive new cache notifications from this Nbh
     *
     * @var bool
     */
    private $notify = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserid()
    {
        return $this->userid;
    }

    public function getUser()
    {
        if (is_null($this->user)) {
            $this->user = User::fromUserIdFactory($this->getUserid());
        }
        return $this->user;
    }

    public function getSeq()
    {
        return $this->seq;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCoords()
    {
        return $this->coords;
    }

    public function getRadius()
    {
        return $this->radius;
    }

    public function getNotify()
    {
        return $this->notify;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    public function setSeq($seq)
    {
        $this->seq = $seq;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setCoords($coords)
    {
        $this->coords = $coords;
    }

    public function setRadius($radius)
    {
        $this->radius = $radius;
    }

    public function setNotify($notify)
    {
        $this->notify = $notify;
    }

    /**
     * Factory
     *
     * @param integer $id - Id of Neighbourhood in DB
     * @return Neighbourhood|null
     */
    public static function fromIdFactory($id)
    {
        $result = new self();
        try {
            $result->loadById($id);
        } catch (\Exception $e) {
            return null;
        }
        return $result;
    }

    /**
     * Returns array of all Neighbourhoods of user.
     * HomeCoords & NotifyRadius has Id & Seq set to 0
     *
     * @param User $user
     * @return Neighbourhood[]
     */
    public static function getNeighbourhoodsList(User $user)
    {
        $result = [];
        // Stage 1 - get default neighbourhood stored in user table
        if ($user->getNotifyRadius() != 0 && $user->getHomeCoordinates()->areCordsReasonable()) {
            $myNgh = new Neighbourhood();
            $myNgh->setId(0);
            $myNgh->setSeq(0);
            $myNgh->setUserid($user->getUserId());
            $myNgh->setName(tr('my_neighborhood'));
            $myNgh->setCoords($user->getHomeCoordinates());
            $myNgh->setRadius($user->getNotifyRadius());
            $myNgh->setNotify($user->getNotifyCaches());
            $myNgh->dataLoaded = true;
            $myNgh->prepareForSerialization();
            $result[0] = $myNgh;
        }
        // Stage 2 - get additional neighbourhoods stored in user_neighbourhoods table
        $myNghAdd = self::getAdditionalNeighbourhoodsList($user);
        foreach ($myNghAdd as $row) {
            $result[$row->getSeq()] = $row;
        }
        return $result;
    }

    public static function getAdditionalNeighbourhoodsList(User $user)
    {
        $query = '
            SELECT `id`
            FROM `user_neighbourhoods`
            WHERE `user_id` = :userid
            ORDER BY seq ASC
            ';
        $params = [];
        $params['userid']['value'] = $user->getUserId();
        $params['userid']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        $tmplist = self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return Neighbourhood::fromIdFactory($row['id']);
        });
        $result = [];
        foreach ($tmplist as $row) {
            $row->prepareForSerialization();
            $result[$row->getSeq()] = $row;
        }
        return $result;
    }

    /**
     * Stores (create lub modify in DB) additional user's neighbourhood
     *
     * @param User $user
     * @param Coordinates $coords
     * @param int $radius
     * @param string $name
     * @param int $seq - if null - get first available number
     * @return boolean
     */
    public static function storeUserNeighbourhood(User $user, Coordinates $coords, $radius, $name, $seq = null, $notify = false)
    {
        if (is_null($seq)) {
            $seq = self::getMaxUserSeq($user) + 1;
        }
        $query = '
            INSERT INTO `user_neighbourhoods`
              (`user_id`, `seq`, `name`, `longitude`, `latitude`, `radius`, `notify`)
            VALUES
              (:1, :2, :3, :4, :5, :6, :7)
            ON DUPLICATE KEY UPDATE
              `name` = :3,
              `longitude` = :4,
              `latitude` = :5,
              `radius` = :6,
              `notify` = :7
            ';
        return (self::db()->multiVariableQuery($query, $user->getUserId(), (int) $seq, $name, $coords->getLongitude(), $coords->getLatitude(), (int) $radius, Php7Handler::Boolval($notify)) !== null);
    }

    /**
     * Removes additional user neighbourhood from DB
     *
     * @param User $user
     * @param int $seq
     * @return boolean
     */
    public static function removeUserNeighbourhood(User $user, $seq)
    {
        $query = '
            DELETE FROM `user_neighbourhoods`
            WHERE `user_id` = :1 AND `seq` = :2
            LIMIT 1
        ';
        $stmt = self::db()->multiVariableQuery($query, $user->getUserId(), $seq);
        if ($stmt == null) {
            return false;
        }
        return (self::db()->rowCount($stmt) == 1);
    }

    /**
     * Returns max seq number for user's additional nbh
     *
     * @param User $user
     * @return int
     */
    private static function getMaxUserSeq(User $user)
    {
        $query = '
            SELECT MAX(`seq`)
            FROM `user_neighbourhoods`
            WHERE `user_id` = :1
        ';
        return self::db()->multiVariableQueryValue($query, 0, $user->getUserId());
    }

    private function loadById($id)
    {
        $query = '
            SELECT *
            FROM `user_neighbourhoods`
            WHERE `id` = :id
            ';
        $params = [];
        $params['id']['value'] = (int) $id;
        $params['id']['data_type'] = 'integer';
        $stmt = $this->db->paramQuery($query, $params);
        $neighbourhoodDbRow = $this->db->dbResultFetch($stmt);
        if (is_array($neighbourhoodDbRow)) {
            $this->loadFromRow($neighbourhoodDbRow);
        } else {
            throw new \Exception("Neighbourhood not found");
        }
    }

    private function loadFromRow(array $neighbourhoodDbRow)
    {
        $this->id = $neighbourhoodDbRow['id'];
        $this->userid = $neighbourhoodDbRow['user_id'];
        $this->user = null;
        $this->seq = $neighbourhoodDbRow['seq'];
        $this->name = $neighbourhoodDbRow['name'];
        $this->coords = Coordinates::FromCoordsFactory($neighbourhoodDbRow['latitude'], $neighbourhoodDbRow['longitude']);
        $this->radius = $neighbourhoodDbRow['radius'];
        $this->notify = $neighbourhoodDbRow['notify'];
        $this->dataLoaded = true;
        return $this;
    }
}