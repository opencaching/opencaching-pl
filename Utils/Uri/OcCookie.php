<?php
namespace Utils\Uri;

use Utils\Debug\Debug;
use Utils\Text\UserInputFilter;
use lib\Objects\User\UserAuthorization;

class OcCookie
{

    private static $ocData = null;
 // data stored in cookie
    private static $changed = false;
 //

    /**
     * Set data in cookie uder the $key
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value, $saveToHeaderNow = false)
    {
        $data = self::getOcCookieData(); // init oc data
        $data->$key = $value;
        self::$ocData = $data;

        if ($saveToHeaderNow) {
            self::saveInHeader();
            self::$changed = false;
        } else {
            self::$changed = true;
        }
    }

    /**
     * Get data from cookie stored under $key
     *
     * @param string $key
     * @return mixed | null - null if no data
     */
    public static function get($key, $skipPurifying = false)
    {
        $data = self::getOcCookieData(); // init oc data
        if (isset($data->$key)) {
            if (! $skipPurifying) {
                return UserInputFilter::purifyHtmlString($data->$key);
            } else {
                return $data->$key;
            }
        } else {
            return null;
        }
    }

    public static function getOrDefault($key, $default)
    {
        if (is_null($val = self::get($key))) {
            return $default;
        } else {
            return $val;
        }
    }

    /**
     * Returns true if given $key is set in cookie
     *
     * @param string $key
     */
    public static function contains($key)
    {
        $data = self::getOcCookieData();
        return isset($data->$key);
    }

    /**
     * Delete data stored under $key in cookie
     *
     * @param string $key
     */
    public static function delete($key, $saveToHeaderNow = false)
    {
        $data = self::getOcCookieData();
        unset($data->$key);
        self::$ocData = $data;

        if ($saveToHeaderNow) {
            self::saveInHeader();
            self::$changed = false;
        } else {
            self::$changed = true;
        }
    }

    public static function debug()
    {
        $data = self::getOcCookieData(); // init oc data
        d($data);
    }

    private static function getOcCookieData()
    {
        if (is_null(self::$ocData)) {
            if (isset($_COOKIE[self::getOcCookieName()])) {
                self::$ocData = json_decode(base64_decode($_COOKIE[self::getOcCookieName()]));
                if(!is_object(self::$ocData)){
                    self::$ocData = new \stdClass();
                }
            } else {
                self::$ocData = new \stdClass();
            }
        }
        return self::$ocData;
    }

    /**
     * init OC-user-data cookie
     */
    public static function saveInHeader()
    {
        $cookieExpiry = time() + UserAuthorization::PERMANENT_LOGIN_TIMEOUT;

        $result = CookieBase::setCookie(
            self::getOcCookieName(),
            base64_encode( json_encode(self::$ocData)),
            $cookieExpiry,
            '/',
            false,
            true,
            CookieBase::SAME_SITE_RESTRICTION_LAX);

        if (! $result) {
            Debug::errorLog(__METHOD__ . ": Can't set OCUserData cookie");
        }
    }

    /**
     * Delete OC-user-data cookie
     */
    private static function deleteOcCookie()
    {
        unset($_COOKIE[self::getOcCookieName()]);

        $result = CookieBase::deleteCookie(self::getOcCookieName());
        if (! $result) {
            Debug::errorLog(__METHOD__ . ": Can't delete OCUserData cookie");
        }
    }

    /**
     * return name of OC-user-data cookie
     */
    private static function getOcCookieName()
    {
        global $config;
        return $config['cookie']['name'] . '-userData';
    }
}

