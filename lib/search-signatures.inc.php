<?php

/* * *************************************************************************
  ./lib/search-signatures.inc.php
  --------------------
  begin                : January 25 2014
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  functions for simple signing request and passing user_id outside

 * ************************************************************************** */
global $usr;

/**
 * Simple class for signing requests, and restoring user data from the signature.
 *
 * Usage pattern
 *
 * Generate link
 * $link = 'http://site.com/do.php?what=somethin'.requestSigner::get_signature_text();
 *
 * Pass the link outside, via any transport means that does not accept cookies
 *
 *
 * In do.php, retrieve original user data
 * $usr = requestSigner::extract_user($usr);
 *
 * if ($usr !== false){
 *     // user is authenticated, either because of session cookie (in precedense),
 *     // or because the link was properly signed
 * }
 *
 * The signature expires in 1 hour from generation time
 */
class requestSigner
{

    /**
     * If the user is logged in, returns signature URL snippet, in form &signature=SOMETHING.
     * Otherwise, empty string is returned
     */
    public static function get_signature_text()
    {
        global $usr;
        if (is_array($usr)) {
            if (isset($_SESSION['signature'] ))
                $signature = $_SESSION['signature'];
            else
                $signature = null;

            if ($signature == null) {
                // TODO grhhh, it's not cryptographically strong RNG
                $signature = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
                $_SESSION['signature'] = $signature;
            }
            apc_store($signature, $usr, 3600);  # cache it for 1 hour
            return '&signature=' . $signature;
        } else {
            return '';
        }
    }

    /**
     * Retrieves user data from the signed request. Use the following code pattern
     *
     *  $usr = requestSigner::extract_user($usr);
     *
     * When this method returns, and request is properly signed, $usr variable
     * is restored with data saved when get_signature_text() was called.
     *
     * It will NOT override current user, if any is set.
     * It will NOT persist user information in a session.
     */
    public static function extract_user($usr)
    {
        if (isset($usr) && is_array($usr)) {
            return $usr;
        }
        if (isset($_GET['signature'])) {
            $signature = $_GET['signature'];
            $user = apc_fetch($signature);
            if ($user) {
                $usr = $user;
            }
        }
        return $usr;
    }

}

?>
