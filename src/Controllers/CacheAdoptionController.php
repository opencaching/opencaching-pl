<?php
namespace src\Controllers;

use src\Models\User\User;
use src\Models\GeoCache\GeoCache;
use src\Utils\Email\EmailSender;
use src\Utils\Database\OcDb;
use src\Utils\Uri\Uri;
use src\Utils\Generators\Uuid;
use src\Models\OcConfig\OcConfig;
use src\Utils\Uri\SimpleRouter;

class CacheAdoptionController extends BaseController
{

    // common error and info message displayed as an actions results
    private $infoMsg = '';
    private $errorMsg = '';

    /** @var OcDb **/
    private $db;

    public function __construct()
    {
        parent::__construct();

        // check if user is authorized
        $this->redirectNotLoggedUsers();

        $this->db = OcDb::instance(); //TODO: create model for cacheAdoption...
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return true;
    }

    /**
     * This is default view - print list of adoption offers for current user + caches owned by current user
     */
    public function index()
    {

        // print list of caches which are offered for current user
        if ( $this->loggedUser->isAdoptionApplicable() ) {

            // there are caches which are waiting for this user adoption decision
            $this->view->setVar('adoptionOffers', $this->getAdoptionOffers() );

        } else {
            // there are caches which are waiting for this user adoption decision
            $this->view->setVar('adoptionOffers',  null);
            $this->infoMsg = tr('adopt_02');
        }

        // print list of caches own by current user which user can offer for adoption
        $this->view->setVar('userCaches', $this->getUserCaches());

        $this->view->setTemplate('cacheAdoption/cacheList');
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/cacheAdoption/cacheAdoption.css'));

        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setVar('infoMsg', $this->infoMsg);

        $this->view->buildView();
    }

    /**
     * accept geocache
     */
    public function accept( $cacheId )
    {

        if( is_null($cacheObj = GeoCache::fromCacheIdFactory( $cacheId )) ){
            $this->index();
            return;
        }

        //check if current user is able to accept cache and has this offer
        if (! $this->loggedUser->isAdoptionApplicable() ||
            ! $this->checkOffer($cacheObj, $this->loggedUser)
            ) {
                // it shouldn't happens - someone try to hack smth?!

                // redirect to main script
                $this->view->redirect(SimpleRouter::getLink(self::class));
                exit;
        }

        // owner changing
        $this->db->beginTransaction();

        // remove all adoption offers for this cache in DB
        $this->db->multiVariableQuery(
            "DELETE FROM chowner WHERE cache_id = :1", $cacheObj->getCacheId());

        // update owner and org_user_id fields for the cache
        $oldOwner = User::fromUserIdFactory($cacheObj->getOwnerId());
        if (is_null($oldOwner)) {
            //no such user?!
            $this->errorMsg = "Old owner not found!";
            return;
        }

        $this->db->multiVariableQuery(
            "UPDATE caches SET user_id = :2, org_user_id = IF(org_user_id IS NULL, :3, org_user_id) WHERE cache_id= :1",
            $cacheObj->getCacheId(), $this->loggedUser->getUserId(), $oldOwner->getUserId());

        // update owner for pictures
        $this->db->multiVariableQuery(
            "UPDATE pictures SET user_id = :2 WHERE object_id = :1",
            $cacheObj->getCacheId(), $this->loggedUser->getUserId());

        // this should be kept consistent by a trigger
        // "UPDATE user SET hidden_count = hidden_count - 1 WHERE user_id = $oldUserId";
        // "UPDATE user SET hidden_count = hidden_count + 1 WHERE user_id = $newUserId";

        // put log into cache logs.

        // TODO: It should be moved from here
        $logMessage = tr('adopt_32');

        $logMessage .= ' <a href="' . $oldOwner->getProfileUrl() . '">' . tr('user') . '</a> -&gt;';
        $logMessage .= ' <a href="' . $this->loggedUser->getProfileUrl() . '">' . tr('user') . '</a>';

        $this->db->multiVariableQuery(
            "INSERT INTO cache_logs SET
                            cache_id = :1,
                            user_id = -1,
                            type = 3,
                            date = NOW(),
                            text= :2,
                            text_html = 2,
                            date_created = NOW(),
                            last_modified = NOW(),
                            uuid = :3,
                            node = :4",

            $cacheObj->getCacheId(), $logMessage, Uuid::create(), OcConfig::getSiteNodeId() );

        $this->db->multiVariableQuery(
            "UPDATE `caches` SET
                `notes`= `notes`+1 WHERE `cache_id` =:1",
            $cacheObj->getCacheId());

        $this->db->commit();

        $message = tr('adopt_15');
        $message = str_replace('{cacheName}', $cacheObj->getCacheName(), $message);
        $this->infoMsg = $message;

        EmailSender::sendAdoptionSuccessMessage(__DIR__.'/../../resources/email/adoption.email.html',
            $cacheObj->getCacheName(), $this->loggedUser->getUserName(), $oldOwner->getUserName(), $oldOwner->getEmail());


        $this->index();

    }

    /**
     * adoption offer was refused
     */
    public function refuse($cacheId)
    {
        if( is_null($cacheObj = GeoCache::fromCacheIdFactory( $cacheId )) ){
            // redirect to main script
            $this->view->redirect(SimpleRouter::getLink(self::class));
            exit;
        }

        // first check if this offer can be refused by this user
        if (!$this->checkOffer($cacheObj, $this->loggedUser)) {
            // it shouldn't happens - someone try to hack smth?!

            // redirect to main script
            $this->view->redirect(SimpleRouter::getLink(self::class));
            exit;
        }

        // user refused to adopt this cache
        $this->db->multiVariableQuery(
            "DELETE FROM chowner WHERE cache_id = :1", $cacheObj->getCacheId());

        $oldOwner = User::fromUserIdFactory($cacheObj->getOwnerId());
        if (!is_null($oldOwner)) {

            $this->infoMsg = tr('adopt_27');
            EmailSender::sendAdoptionRefusedMessage(__DIR__.'/../../resources/email/adoption.email.html',
                $cacheObj->getCacheName(), $this->loggedUser->getUserName(), $oldOwner->getUserName(), $oldOwner->getEmail());
        }

        $this->index();
    }

    /**
     * adoption offer was canceled
     */
    public function abort($cacheId)
    {
        if( is_null($cacheObj = GeoCache::fromCacheIdFactory( $cacheId )) ){
            // redirect to main script
            $this->view->redirect(SimpleRouter::getLink(self::class));
            exit;
        }

        // check if current user is an owner of selected cache
        if ($this->loggedUser->getUserId() == $cacheObj->getOwnerId() ) {

            // old owner of the cache cancel adoption offer
            $this->db->multiVariableQuery(
                "DELETE FROM chowner WHERE cache_id = :1", $cacheObj->getCacheId() );

            $this->infoMsg = tr('adopt_16');
        } else {
            $this->errorMsg = tr('adopt_35_notOwner');
        }

        // redirect to main script
        $this->view->redirect(SimpleRouter::getLink(self::class));
        exit;
    }

    /**
     * user created new adoption offer - save it!
     */
    public function addAdoptionOffer($cacheId)
    {
        $cacheObj = GeoCache::fromCacheIdFactory($cacheId);
        if(is_null($cacheObj)){
            $this->index();
            exit;
        }

        if( !isset( $_POST['username']) || is_null($newUserObj = User::fromUsernameFactory($_POST['username']) ) ) {
            // redirect to main script
            $this->view->redirect(SimpleRouter::getLink(self::class));
            exit;
        }

        //first check if current user is an owner of cache for adoption
        if ($this->loggedUser->getUserId() != $cacheObj->getOwnerId() ) {
            //it shouldn't happens - someone hack us?!

            // redirect to main script
            $this->view->redirect(SimpleRouter::getLink(self::class));
            exit;
        }

        // check if the new owner is not the old owner :)
        if ($newUserObj->getUserId() == $cacheObj->getOwnerId() ) {
            $this->errorMsg = tr('adopt_33');

        } else {

            // user exists and is not current owner of this cache

            //check if user is able to adopt caches
            if (!$newUserObj->isAdoptionApplicable()) {
                $this->errorMsg = tr('adopt_34');
                $this->index();
                exit;
            }

            //check if there is no such offer
            if ($this->checkOffer( $cacheObj )) {
                $this->errorMsg = "There is such adoption offer already!";
                $this->index();
                exit;
            }

            $stmt = $this->db->multiVariableQuery(
                "INSERT INTO chowner (cache_id, user_id) VALUES ( :1, :2)",
                $cacheObj->getCacheId(), $newUserObj->getUserId());

            if ($this->db->rowCount($stmt) > 0) {
                EmailSender::sendAdoptionOffer(__DIR__.'/../../resources/email/adoption.email.html', $cacheObj->getCacheName(),
                    $newUserObj->getUserName(), $this->loggedUser->getUserName(), $newUserObj->getEmail());
                $this->infoMsg = tr('adopt_24');
            } else {
                $this->errorMsg = tr('adopt_22');
            }
        }

        $this->index();
    }

    /**
     *  Display view which allow to select user for the new cache adoption offer
     *  TODO: it should be done in dialog instead of new view
     */
    public function selectUser($cacheId)
    {
        $cacheObj = GeoCache::fromCacheIdFactory($cacheId);
        if(is_null($cacheObj)){
            $this->index();
            exit;
        }

        $this->view->setTemplate('cacheAdoption/chooseUser');
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/cacheAdoption/cacheAdoption.css'));

        $this->view->setVar('listOfCachesUrl', SimpleRouter::getLink(CacheAdoptionController::class));
        $this->view->setVar('cacheObj', $cacheObj);

        $this->view->buildView();
        exit;
    }

    /**
     * // check if there is an adoption offer for this cache and this user
     *
     * @param GeoCache $cacheObj -
     * @param User $userObj - optional
     * @return bool
     */
    private function checkOffer(GeoCache $cacheObj, User $userObj = null)
    {
        if ($userObj == null) {
            $offers = $this->db->multiVariableQueryValue(
                "SELECT COUNT(*) FROM chowner WHERE cache_id = :1 LIMIT 1",
                0, $cacheObj->getCacheId() );
        } else {
            $offers = $this->db->multiVariableQueryValue(
                "SELECT COUNT(*) FROM chowner WHERE cache_id = :1 AND user_id = :2 LIMIT 1",
                0, $cacheObj->getCacheId(), $userObj->getUserId() );
        }
        return ( $offers> 0 ) ? true : false;
    }

    private function getUserCaches()
    {
        // lists all approved caches belonging to user + id of adoption offer (if present)
        return $this->db->dbResultFetchAll(
            $this->db->multiVariableQuery(
            "SELECT c.cache_id, name, chowner.id AS adoptionOfferId, username AS offeredToUserName
             FROM caches AS c LEFT JOIN chowner
                ON chowner.cache_id = c.cache_id
                LEFT JOIN user ON chowner.user_id = user.user_id
             WHERE c.user_id= :1
                AND status <> 4
             ORDER BY name",
            $this->loggedUser->getUserId() ));
    }

    private function getAdoptionOffers()
    {
        return $this->db->dbResultFetchAll(
            $this->db->multiVariableQuery(
            "SELECT cache_id, name, date_hidden, username AS offeredFromUserName
            FROM caches LEFT JOIN user
                ON caches.user_id = user.user_id
            WHERE cache_id IN (
                SELECT cache_id FROM chowner WHERE user_id = :1
            )",
            $this->loggedUser->getUserId()));
    }
}
