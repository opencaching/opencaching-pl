<?php
/**
 * This script allow user to:
 * - create adoption offer
 * - accept or refuse offer from other users
 * - abort adoption offer created before
 *
 */


use Utils\Database\XDb;
use Utils\Database\OcDb;
use lib\Objects\User\User;
use lib\Objects\GeoCache\GeoCache;
use Utils\Email\EmailSender;

require_once 'lib/common.inc.php';

class ChownerController
{

    // common error and info message displayed as an actions results
    private $infoMsg = '';
    private $errorMsg = '';

    /* @var User $userObj */
    private $userObj;

    /* @var OcDb $db */
    private $db;

    public function __construct(){

        global $error;
        global $usr;

        // check if user is authorized
        if ($error != false && !isset($usr['userid'])) {
            // redirect non-logged users
            header("Location: index.php");
            return;
        }

        $this->userObj = User::fromUserIdFactory($usr['userid']);
        $this->db = OcDb::instance();

        if( isset($_GET['action']) ){

            if( isset ( $_REQUEST['cacheid'] ) ){

                // retrive cache information
                /* @var $cacheObj GeoCache */
                $cacheObj = GeoCache::fromCacheIdFactory( $_REQUEST['cacheid'] );

                switch($_GET['action']){
                    case 'accept':
                        $this->actionAccept($cacheObj);
                        break;
                    case 'refuse':
                        $this->actionRefuse($cacheObj);
                        break;
                    case 'abort':
                        $this->actionAbort($cacheObj);
                        break;
                    case 'selectUser':
                        $this->actionSelectUser($cacheObj);
                        break;
                    case 'addAdoptionOffer':
                        if( isset( $_POST['username'] ) ){

                            /* @var User */
                            $newUserObj = User::fromUsernameFactory($_POST['username']);
                            if (! $newUserObj->isDataLoaded()) {

                                // no such user or different error during loading from DB
                                $this->errorMsg = str_replace('{userName}', $_POST['username'], tr('adopt_23'));

                            } else {
                                $this->actionAddAdoptionOffer($newUserObj, $cacheObj);
                            }

                        }else{
                            $this->errorMsg = str_replace('{userName}', $_POST['username'], tr('adopt_23'));
                        }
                        break;
                    default:
                        header("Location: chowner.php");
                        exit;
                }
            }else{
                header("Location: chowner.php");
                exit;
            }
        }

        //display main view after all...
        $this->mainView();
    }

    /**
     * accept geocache
     */
    private function actionAccept(GeoCache $cacheObj){

        //check if current user is able to accept cache and has this offer
        if( ! $this->userObj->isAdoptionApplicable() ||
            ! $this->checkOffer($cacheObj, $this->userObj)
        ){
            // it shouldn't happens - someone try to hack smth?!

            // redirect to main script
            header("Location: chowner.php");
            return;
        }

        // owner changing
        $this->db->beginTransaction();

        // populate org cache user for this cache
        //TODO: this is strange... needs investigation
        global $rootpath;
        require_once ($rootpath . 'lib/cache_owners.inc.php');
        $pco = new OrgCacheOwners($this->db);
        $pco->populateForCache( $cacheObj->getCacheId() );

        // remove all adoption offers for this cache in DB
        $this->db->multiVariableQuery(
            "DELETE FROM chowner WHERE cache_id = :1", $cacheObj->getCacheId());

        // update owner and org_user_id fields for the cache
        $oldOwner = User::fromUserIdFactory($cacheObj->getOwnerId());

        $this->db->multiVariableQuery(
            "UPDATE caches SET user_id = :2, org_user_id = IF(org_user_id IS NULL, :3, org_user_id) WHERE cache_id= :1",
            $cacheObj->getCacheId(), $this->userObj->getUserId(), $oldOwner->getUserId());

        // update owner for pictures
        $this->db->multiVariableQuery(
            "UPDATE pictures SET user_id = :2 WHERE object_id = :1",
            $cacheObj->getCacheId(), $this->userObj->getUserId());

        // this should be kept consistent by a trigger
        // $q = "UPDATE user SET hidden_count = hidden_count - 1 WHERE user_id = $oldUserId";
        // $q = "UPDATE user SET hidden_count = hidden_count + 1 WHERE user_id = $newUserId";

        // put log into cache logs.
        $logMessage = tr('adopt_32');

        $oldUserName = ' <a href="' . $GLOBALS['absolute_server_URI'] . 'viewprofile.php?userid=' . $oldOwner->getUserId() . '">' . $oldOwner->getUserName() . '</a> ';
        $newUserName = ' <a href="' . $GLOBALS['absolute_server_URI'] . 'viewprofile.php?userid=' . $this->userObj->getUserId() . '">' . $this->userObj->getUserName() . '</a>';

        $logMessage = str_replace('{oldUserName}', $oldUserName, $logMessage);
        $logMessage = str_replace('{newUserName}', $newUserName, $logMessage);

        $this->db->multiVariableQuery(
            "INSERT INTO cache_logs SET
                            cache_id = :1,
                            user_id = -1,
                            type = 3,
                            date = NOW(),
                            text= :2,
                            text_html = 1,
                            text_htmledit = 1,
                            date_created = NOW(),
                            last_modified = NOW(),
                            uuid = :3,
                            node = :4",

            $cacheObj->getCacheId(), $logMessage, create_uuid(), $GLOBALS['oc_nodeid'] );

        $this->db->multiVariableQuery(
            "UPDATE `caches` SET
                `notes`= `notes`+1 WHERE `cache_id` =:1",
            $cacheObj->getCacheId());

        $this->db->commit();

        $message = tr('adopt_15');
        $message = str_replace('{cacheName}', $cacheObj->getCacheName(), $message);
        $this->infoMsg = $message;

        EmailSender::sendAdoptionSuccessMessage(__DIR__ . '/tpl/stdstyle/email/adoption.email.html',
        $cacheObj->getCacheName(), $this->userObj->getUserName(), $oldOwner->getUserName(), $oldOwner->getEmail());

    }

    /**
     * adoption offer was refused
     */
    private function actionRefuse(GeoCache $cacheObj){

        // first check if this offer can be refused by this user
        if(!$this->checkOffer($cacheObj, $this->userObj)){
            // it shouldn't happens - someone try to hack smth?!

            // redirect to main script
            header("Location: chowner.php");
            return;
        }

        // user refused to adopt this cache
        $s = $this->db->multiVariableQuery(
            "DELETE FROM chowner WHERE cache_id = :1", $cacheObj->getCacheId());

        $oldOwner = User::fromUserIdFactory($cacheObj->getOwnerId());

        $this->infoMsg = tr('adopt_27');
        EmailSender::sendAdoptionRefusedMessage(__DIR__ . '/tpl/stdstyle/email/adoption.email.html',
            $cacheObj->getCacheName(), $this->userObj->getUserName(), $oldOwner->getUserName(), $oldOwner->getEmail());
    }

    /**
     * adoption offer was canceled
     */
    private function actionAbort(GeoCache $cacheObj){

        // check if current user is an owner of selected cache
        if( $this->userObj->getUserId() == $cacheObj->getOwnerId() ){

            // old owner of the cache cancel adoption offer
            $s = $this->db->multiVariableQuery(
                "DELETE FROM chowner WHERE cache_id = :1", $cacheObj->getCacheId() );

            $this->infoMsg = tr('adopt_16');
        }else{
            $this->errorMsg = tr('adopt_35_notOwner');
        }
    }

    /**
     * user created new adoption offer - save it!
     */
    private function actionAddAdoptionOffer(User $newUserObj, GeoCache $cacheObj){
        //first check if current user is an owner of cache for adoption
        if( $this->userObj->getUserId() != $cacheObj->getOwnerId() ){
            //it shouldn't happens - someone hack us?!

            // redirect to main script
            header("Location: chowner.php");
            return;
        }

        // check if the new owner is not the old owner :)
        if( $newUserObj->getUserId() == $cacheObj->getOwnerId() ){
            $this->errorMsg = tr('adopt_33');

        } else {

            // user exists and is not current owner of this cache

            //check if user is able to adopt caches
            if(!$newUserObj->isAdoptionApplicable()){
                $this->errorMsg = tr('adopt_34');
                return;
            }

            //check if there is no such offer
            if( $this->checkOffer( $cacheObj )) {
                $this->errorMsg = "There is such adoption offer already!";
                return;
            }

            $stmt = XDb::xSql(
                "INSERT INTO chowner (cache_id, user_id) VALUES ( ?, ?)",
                $cacheObj->getCacheId(), $newUserObj->getUserId());

            if (XDb::xNumRows($stmt) > 0) {
                EmailSender::sendAdoptionOffer(__DIR__ . '/tpl/stdstyle/email/adoption.email.html', $cacheObj->getCacheName(),
                    $newUserObj->getUserName(), $this->userObj->getUserName(), $newUserObj->getEmail());
                $this->infoMsg = tr('adopt_24');
            } else {
                $this->errorMsg = tr('adopt_22');
            }
        }
    }

    /**
     *  Display view which allow to select user for the new cache adoption offer
     *  TODO: it should be done in dialog instead of new view
     */
    private function actionSelectUser(GeoCache $cacheObj){

        tpl_set_var ( 'cachename', $cacheObj->getCacheName() );
        tpl_set_var ( 'cacheid', $cacheObj->getCacheId() );
        tpl_set_tplname('chowner_chooseuser');

        tpl_BuildTemplate();
        exit;
    }

    /**
     * This is default view - print list of adoption offers for current user + caches owned by current user
     */
    public function mainView(){

        // print list of caches which are offered for current user
        if ( $this->userObj->isAdoptionApplicable() ){

            // there are caches which are waiting for this user adoption decision
            setViewVar('adoptionOffers', $this->getAdoptionOffers() );

        }else{
            // there are caches which are waiting for this user adoption decision
            setViewVar('adoptionOffers',  null);
            $this->infoMsg = tr('adopt_02');
        }

        // print list of caches own by current user which user can offer for adoption

        setViewVar('userCaches', $this->getUserCaches() );

        tpl_set_tplname('chowner');

        setViewVar('errorMsg', $this->errorMsg);
        setViewVar('infoMsg', $this->infoMsg);

        tpl_BuildTemplate();
    }


    /**
     * // check if there is an adoption offer for this cache and this user
     *
     * @param GeoCache $cacheObj -
     * @param User $userObj - optional
     * @return bool
     */
    private function checkOffer(GeoCache $cacheObj, User $userObj = null ){

        if($userObj == null){
            $offers = $this->db->multiVariableQueryValue(
                "SELECT COUNT(*) FROM chowner WHERE cache_id = :1 LIMIT 1",
                0, $cacheObj->getCacheId() );
        }else{
            $offers = $this->db->multiVariableQueryValue(
                "SELECT COUNT(*) FROM chowner WHERE cache_id = :1 AND user_id = :2 LIMIT 1",
                0, $cacheObj->getCacheId(), $userObj->getUserId() );
        }
        return ( $offers> 0 )?true:false;
    }

    private function getUserCaches(){

        // lists all approved caches belonging to user + id of adoption offer (if present)
        $rs = $this->db->multiVariableQuery(
            "SELECT c.cache_id, name, chowner.id AS adoptionOfferId, username AS offeredToUserName
             FROM caches AS c LEFT JOIN chowner
                ON chowner.cache_id = c.cache_id
                LEFT JOIN user ON chowner.user_id = user.user_id
             WHERE c.user_id= :1
                AND status <> 4
             ORDER BY name",
            $this->userObj->getUserId() );

        return $this->db->dbResultFetchAll($rs);
    }

    private function getAdoptionOffers(){

        $rs = $this->db->multiVariableQuery(
            "SELECT cache_id, name, date_hidden, username AS offeredFromUserName
            FROM caches LEFT JOIN user
                ON caches.user_id = user.user_id
            WHERE cache_id IN (
                SELECT cache_id FROM chowner WHERE user_id = :1
            )",
            $this->userObj->getUserId());

        return $this->db->dbResultFetchAll($rs);
    }
}

// temporary call from here
new ChownerController();
