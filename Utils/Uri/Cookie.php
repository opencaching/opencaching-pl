<?php
namespace Utils\Uri;

/**
 * This class is alternative to PHP setcookie, which allow to set SameSize param
 *
 * Most of the code is based (thanks for that) on:
 *
 * PHP-Cookie (https://github.com/delight-im/PHP-Cookie)
 * Copyright (c) delight.im (https://www.delight.im/)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */
class Cookie
{

    const HEADER_PREFIX = 'Set-Cookie: ';

    const SAME_SITE_RESTRICTION_LAX = 'Lax';

    const SAME_SITE_RESTRICTION_STRICT = 'Strict';

    /**
     * Deletes the cookie for current domain
     *
     * @return bool whether the cookie header has successfully been sent (and will *probably* cause the client to delete the cookie)
     */
    public static function deleteCookie($name)
    {
        $cookieDomain = Cookie::normalizeDomain(Uri::getCurrentDomain());
        return setcookie($name, '', 0, '/', $cookieDomain);
    }

    /**
     * Sets a new cookie in a way compatible to PHP's `setcookie(...)` function
     *
     * @param string $name
     *            the name of the cookie which is also the key for future accesses via `$_COOKIE[...]`
     * @param mixed|null $value
     *            the value of the cookie that will be stored on the client's machine
     * @param int $expiryTime
     *            the Unix timestamp indicating the time that the cookie will expire at, i.e. usually `time() + $seconds`
     * @param string|null $path
     *            the path on the server that the cookie will be valid for (including all sub-directories), e.g. an empty string for the current directory or `/` for the root directory
     * @param bool $secureOnly
     *            indicates that the cookie should be sent back by the client over secure HTTPS connections only
     * @param bool $httpOnly
     *            indicates that the cookie should be accessible through the HTTP protocol only and not through scripting languages
     * @param string|null $sameSiteRestriction
     *            indicates that the cookie should not be sent along with cross-site requests (either `null`, `Lax` or `Strict`)
     * @return bool whether the cookie header has successfully been sent (and will *probably* cause the client to set the cookie)
     */
    public static function setCookie($name, $value = null, $expiryTime = 0,
        $path = null, $secureOnly = false,
        $httpOnly = false, $sameSiteRestriction = null)
    {

        $domain = self::normalizeDomain(Uri::getCurrentDomain());

        return self::addHttpHeader(
            self::buildCookieHeader(
                $name, $value, $expiryTime, $path, $domain,
                $secureOnly, $httpOnly, $sameSiteRestriction));
    }


    private static function buildCookieHeader(
        $name, $value = null, $expiryTime = 0, $path = null, $domain = null,
        $secureOnly = false, $httpOnly = false, $sameSiteRestriction = null
    ){

        if (self::isNameValid($name)) {
            $name = (string) $name;
        } else {
            return null;
        }

        if (self::isExpiryTimeValid($expiryTime)) {
            $expiryTime = (int) $expiryTime;
        } else {
            return null;
        }

        $forceShowExpiry = false;

        if (is_null($value) || $value === false || $value === '') {
            $value = 'deleted';
            $expiryTime = 0;
            $forceShowExpiry = true;
        }

        $maxAgeStr = self::formatMaxAge($expiryTime, $forceShowExpiry);
        $expiryTimeStr = self::formatExpiryTime($expiryTime, $forceShowExpiry);

        $headerStr = self::HEADER_PREFIX . $name . '=' . \urlencode($value);

        if (! is_null($expiryTimeStr)) {
            $headerStr .= '; expires=' . $expiryTimeStr;
        }

        if (! is_null($maxAgeStr)) {
            $headerStr .= '; Max-Age=' . $maxAgeStr;
        }

        if (! empty($path) || $path === 0) {
            $headerStr .= '; path=' . $path;
        }

        if (! empty($domain) || $domain === 0) {
            $headerStr .= '; domain=' . $domain;
        }

        if ($secureOnly) {
            $headerStr .= '; secure';
        }

        if ($httpOnly) {
            $headerStr .= '; httponly';
        }

        if ($sameSiteRestriction === self::SAME_SITE_RESTRICTION_LAX) {
            $headerStr .= '; SameSite=Lax';
        } elseif ($sameSiteRestriction === self::SAME_SITE_RESTRICTION_STRICT) {
            $headerStr .= '; SameSite=Strict';
        }

        return $headerStr;
    }

    private static function isNameValid($name)
    {
        $name = (string) $name;

        // The name of a cookie must not be empty
        if ($name !== '') {
            if (! preg_match('/[=,; \\t\\r\\n\\013\\014]/', $name)) {
                return true;
            }
        }

        return false;
    }

    private static function isExpiryTimeValid($expiryTime)
    {
        return is_numeric($expiryTime) || is_null($expiryTime) || is_bool($expiryTime);
    }

    private static function calculateMaxAge($expiryTime)
    {
        if ($expiryTime === 0) {
            return 0;
        } else {
            $maxAge = $expiryTime - time();

            // The value of the `Max-Age` property must not be negative
            if ($maxAge < 0) {
                $maxAge = 0;
            }

            return $maxAge;
        }
    }

    private static function formatExpiryTime($expiryTime, $forceShow = false)
    {
        if ($expiryTime > 0 || $forceShow) {
            if ($forceShow) {
                $expiryTime = 1;
            }

            return gmdate('D, d-M-Y H:i:s T', $expiryTime);
        } else {
            return null;
        }
    }

    private static function formatMaxAge($expiryTime, $forceShow = false)
    {
        if ($expiryTime > 0 || $forceShow) {
            return (string) self::calculateMaxAge($expiryTime);
        } else {
            return null;
        }
    }

    public static function normalizeDomain($domain, $keepWww = false)
    {
        // make sure the domain is actually a string
        $domain = (string) $domain;

        // if the cookie should be valid for the current host only
        if ($domain === '') {
            // no need for further normalization
            return null;
        }

        // if the provided domain is actually an IP address
        if (filter_var($domain, FILTER_VALIDATE_IP) !== false) {
            // let the cookie be valid for the current host
            return null;
        }

        // for local hostnames (which either have no dot at all or a leading dot only)
        if (strpos($domain, '.') === false || strrpos($domain, '.') === 0) {
            // let the cookie be valid for the current host while ensuring maximum compatibility
            return null;
        }

        // unless the domain already starts with a dot
        if ($domain[0] !== '.') {
            // prepend a dot for maximum compatibility (e.g. with RFC 2109)
            $domain = '.' . $domain;
        }

        // if a leading `www` subdomain may be dropped
        if (! $keepWww) {
            // if the domain name actually starts with a `www` subdomain
            if (substr($domain, 0, 5) === '.www.') {
                // strip that subdomain
                $domain = substr($domain, 4);
            }
        }

        // return the normalized domain
        return $domain;
    }

    private static function addHttpHeader($header)
    {
        if (! headers_sent()) {
            if (! empty($header)) {
                header($header, false);

                return true;
            }
        }

        return false;
    }
}

