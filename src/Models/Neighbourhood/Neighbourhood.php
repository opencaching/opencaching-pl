<?php

namespace src\Models\Neighbourhood;

use Exception;
use src\Models\BaseObject;
use src\Models\Coordinates\Coordinates;
use src\Models\User\User;

class Neighbourhood extends BaseObject
{
    public const ITEM_MAP = 1;

    public const ITEM_LATESTCACHES = 2;

    public const ITEM_UPCOMINGEVENTS = 3;

    public const ITEM_FTFCACHES = 4;

    public const ITEM_LATESTLOGS = 5;

    public const ITEM_TITLEDCACHES = 6;

    public const ITEM_RECOMMENDEDCACHES = 7;

    // An array of Neighbourhood sections with corresponding translation keys
    public const SECTIONS = [
        self::ITEM_MAP => 'map',
        self::ITEM_LATESTCACHES => 'newest_caches',
        self::ITEM_UPCOMINGEVENTS => 'incomming_events',
        self::ITEM_FTFCACHES => 'ftf_awaiting',
        self::ITEM_LATESTLOGS => 'latest_logs',
        self::ITEM_TITLEDCACHES => 'startPage_latestTitledCaches',
        self::ITEM_RECOMMENDEDCACHES => 'top_recommended',
    ];

    /** Id in DB */
    private int $id;

    private int $userid;

    /** User who neighbourhood belongs to */
    private ?User $user = null;

    /** Number in neighbourhoods sequence */
    private int $seq;

    /** Name of neighbourhood */
    private string $name;

    /** Coords of neighbourhood's center */
    private Coordinates $coords;

    /** Radius of neighbourhood */
    private int $radius;

    /** User will receive new cache notifications from this Nbh */
    private bool $notify = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserid(): int
    {
        return $this->userid;
    }

    public function getUser(): ?User
    {
        if (is_null($this->user)) {
            $this->user = User::fromUserIdFactory($this->getUserid());
        }

        return $this->user;
    }

    public function getSeq(): int
    {
        return $this->seq;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCoords(): Coordinates
    {
        return $this->coords;
    }

    public function getRadius(): int
    {
        return $this->radius;
    }

    public function getNotify(): bool
    {
        return $this->notify;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setUserid(int $userid)
    {
        $this->userid = $userid;
    }

    public function setSeq(int $seq)
    {
        $this->seq = $seq;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setCoords(Coordinates $coords)
    {
        $this->coords = $coords;
    }

    public function setRadius(int $radius)
    {
        $this->radius = $radius;
    }

    public function setNotify(bool $notify)
    {
        $this->notify = $notify;
    }

    /**
     * Factory
     *
     * @param int $id - ID of Neighbourhood in DB
     */
    public static function fromIdFactory(int $id): ?Neighbourhood
    {
        $result = new self();

        try {
            $result->loadById($id);
        } catch (Exception $e) {
            return null;
        }

        return $result;
    }

    /**
     * Returns simple array with Coords obj and radius - for given $user and
     * nbhSeq
     */
    public static function getCoordsAndRadius(User $user, int $seq): array
    {
        $result = [];
        $result['coords'] = null;
        $result['radius'] = null;

        if ($seq == 0) {
            if (! empty($user->getHomeCoordinates())) {
                $result['coords'] = $user->getHomeCoordinates();
                $result['radius'] = $user->getNotifyRadius();
            }
        } else {
            $query = '
                SELECT `id`
                FROM `user_neighbourhoods`
                WHERE `user_id` = :1
                    AND `seq` = :2
                LIMIT 1';
            $nbhId = self::db()->multiVariableQueryValue(
                $query,
                null,
                $user->getUserId(),
                $seq
            );
            $nbh = self::fromIdFactory($nbhId);

            if (empty($nbh)) {
                return $result;
            }
            $result['coords'] = $nbh->getCoords();
            $result['radius'] = $nbh->getRadius();
            unset($nbh);
        }

        return $result;
    }

    /**
     * Returns array of all Neighbourhoods of user.
     * HomeCoords & NotifyRadius has ID & Seq set to 0
     *
     * @return Neighbourhood[]
     */
    public static function getNeighbourhoodsList(User $user): array
    {
        $result = [];
        // Stage 1 - get default neighbourhood stored in user table
        if (
            $user->getNotifyRadius() != 0
            && $user->getHomeCoordinates()->areCordsReasonable()
        ) {
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
        // Stage 2 - get additional neighbourhoods stored in user_neighbourhoods
        // table
        $myNghAdd = self::getAdditionalNeighbourhoodsList($user);

        foreach ($myNghAdd as $row) {
            $result[$row->getSeq()] = $row;
        }

        return $result;
    }

    public static function getAdditionalNeighbourhoodsList(User $user): array
    {
        $query = '
            SELECT `id`
            FROM `user_neighbourhoods`
            WHERE `user_id` = :userid
            ORDER BY `seq`
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
     * @param int|null $seq - if null - get first available number
     */
    public static function storeUserNeighbourhood(
        User $user,
        Coordinates $coords,
        int $radius,
        string $name,
        int $seq = null,
        bool $notify = false
    ): bool {
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

        return self::db()->multiVariableQuery(
            $query,
            $user->getUserId(),
            (int) $seq,
            $name,
            $coords->getLongitude(),
            $coords->getLatitude(),
            $radius,
            (int) $notify
        ) !== null;
    }

    /**
     * Removes additional user neighbourhood from DB
     */
    public static function removeUserNeighbourhood(User $user, int $seq): bool
    {
        $query = '
            DELETE FROM `user_neighbourhoods`
            WHERE `user_id` = :1 AND `seq` = :2
            LIMIT 1
        ';
        $stmt = self::db()->multiVariableQuery(
            $query,
            $user->getUserId(),
            $seq
        );

        if ($stmt == null) {
            return false;
        }

        return self::db()->rowCount($stmt) == 1;
    }

    /**
     * Remove all neighbourhoods of given user
     */
    public static function removeAllUserNeighbourhood(User $user): void
    {
        self::db()->multiVariableQuery(
            'DELETE FROM user_neighbourhoods WHERE user_id = :1',
            $user->getUserId()
        );
    }

    /**
     * Changes "Notify" state for $seq Neighbourhood for $user
     */
    public static function setNeighbourhoodNotify(
        User $user,
        int $seq,
        bool $state
    ): bool {
        return null !== self::db()->multiVariableQuery('
            UPDATE `user_neighbourhoods`
            SET `notify` = :1
            WHERE `user_id` = :2
                AND `seq` = :3
            LIMIT 1
        ', intval($state), $user->getUserId(), $seq);
    }

    /**
     * Returns max seq number for user's additional nbh
     */
    private static function getMaxUserSeq(User $user): int
    {
        $query = '
            SELECT MAX(`seq`)
            FROM `user_neighbourhoods`
            WHERE `user_id` = :1
        ';

        return self::db()->multiVariableQueryValue(
            $query,
            0,
            $user->getUserId()
        );
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
            throw new Exception('Neighbourhood not found');
        }
    }

    private function loadFromRow(array $neighbourhoodDbRow): self
    {
        $this->id = $neighbourhoodDbRow['id'];
        $this->userid = $neighbourhoodDbRow['user_id'];
        $this->user = null;
        $this->seq = $neighbourhoodDbRow['seq'];
        $this->name = $neighbourhoodDbRow['name'];
        $this->coords = Coordinates::FromCoordsFactory(
            $neighbourhoodDbRow['latitude'],
            $neighbourhoodDbRow['longitude']
        );
        $this->radius = $neighbourhoodDbRow['radius'];
        $this->notify = $neighbourhoodDbRow['notify'];
        $this->dataLoaded = true;

        return $this;
    }
}
