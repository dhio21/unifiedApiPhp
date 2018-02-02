<?php
/**
 * Created by IntelliJ IDEA.
 * User: faisa
 * Date: 1/26/2018
 * Time: 10:06 PM
 */

namespace api;

use api\dto\UApiUser;


class CookieStore
{
    const COOKIE_APP_NAME = "uapi_app";

    const COOKIE_KEY_PLAT_DBX = "dropbox";
    const COOKIE_KEY_PLAT_GGL = "googleDrive";
    const COOKIE_KEY_PLAT_ONE_DRV = "oneDrive";

    function __construct()
    {
        if (!isset($_COOKIE[self::COOKIE_APP_NAME])) {
            $cookieData = array(
                self::COOKIE_KEY_PLAT_DBX => '',
                self::COOKIE_KEY_PLAT_GGL => '',
                self::COOKIE_KEY_PLAT_ONE_DRV => '',
            );
            setcookie(self::COOKIE_APP_NAME, json_encode($cookieData) );
        }
    }


    public function addToken ( $platform, $token )
    {
        $cookieData = json_decode($_COOKIE[self::COOKIE_APP_NAME], true);
        $cookieData[$platform] = $token;
        setcookie(self::COOKIE_APP_NAME, json_encode($cookieData) );
    }

    public function getToken ( $platform )
    {
        $cookieData = json_decode ( $_COOKIE[self::COOKIE_APP_NAME], true );
        return $cookieData[$platform];
    }

    public function platformHasToken ( $platform ) {
        $cookieData = json_decode($_COOKIE[self::COOKIE_APP_NAME], true);
        return strcmp( $cookieData[$platform], '' ) !== 0;
    }

    public function clear () {
        setcookie( self::COOKIE_APP_NAME, null );
    }

}