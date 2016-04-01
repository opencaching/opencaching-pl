<?php
/**
 * This class provides simple mechanism for
 * prevent generation of too many emails sending by oc
 */

namespace Utils\Email;

class OcSpamDomain {

    //domains - different error-trigger errors can have different domians
    //TODO: it should be rewrite to some kind of enum
    const GENERIC_ERRORS = 'genericError';
    const DB_ERRORS = 'dbError';

    /**
     * Answer if the email can be sent by OC
     *
     * @param enum $domain - only predefined domains are supported
     * @return boolean - true if email can be sent
     */
    public static function isEmailAllowed($domain){

        $lockFile = self::getLockFile($domain);
        if(is_null($lockFile)){
            //unknown domain - don't send anything
            return false;
        }

        if ( file_exists($lockFile) ){

            $lastEmail = filemtime($lockFile);
            $timeout = self::getTimeout($domain);

            if (time() - $lastEmail < $timeout) {
                //timeout not expired - don't send anything
                return false;
            }
        }else{
            //set permissions to allow touch file for apache|cron scripts
            touch($lockFile);
            chmod($lockFile,0666);
        }

        touch($lockFile);
        clearstatcache(); //clear cached info about this file...
        return true;
    }

    /**
     * This function returns timeout used by error-spam-domain
     * Only predefined domains are supported
     *
     * TODO: it should be read from configuration in the future
     *
     * @param const $domain - domain const
     * @return int - timeout in sec.
     */
    private static function getTimeout($domain)
    {
        switch ($domain){
            case self::DB_ERRORS:
                return 60;
                break;

            case self::GENERIC_ERRORS:
                return 60;
                break;

            default:
                trigger_error(__METHOD__.": Unknown ocSpam domain: ".$domain, E_USER_WARNING);
                return 1800; //one-per-hour
        }
    }

    /**
     * This function returns lock file used by error-spam-domain
     * Only predefined domains are supported
     *
     * TODO: it should be read from configuration in the future
     *
     * @param const $domain - domain const
     * @return string|NULL
     */
    private static function getLockFile($domain)
    {
        $filename = '';
        switch ($domain){
            case self::DB_ERRORS:
                $filename = 'ocSpamDomain-db-errors-emails.lock';
                break;

            case self::GENERIC_ERRORS:
                $filename = 'ocSpamDomain-generic-errors-emails.lock';
                break;

            // Add another domain config here
            default:
                //no such domain - trigger warning
                trigger_error(__METHOD__.": Unknown ocSpam domain: ".$domain, E_USER_WARNING);
                return null;
        }

        return sys_get_temp_dir().'/'.$filename;
    }
}