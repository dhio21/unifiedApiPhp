<?php
namespace api;

require "DbConnection.php";
require "dto/UApiUser.php";
require "DropboxDA.php";
require "GoogleDriveDA.php";
require "OneDriveDA.php";
require "CookieStore.php";
require "./vendor/autoload.php";

class UApi
{

    const PLATFORM_DROPBOX = "dbx";
    const PLATFORM_GOOGLE_DRIVE = "ggl";
    const PLATFORM_ONE_DRIVE = "1drv";

    private $toggleDbx = false, $toggleOneDrive = false, $toggleGoogleDrive = false;
    private $cookieStorage ;

    private $dropboxDa;
    private $googleDriveDa;
    private $oneDriveDa;

    function __construct ( $toggleDbx, $toggleOneDrive, $toggleGoogleDrive, $fromConfig = false ) {
        $this->toggleDbx = $toggleDbx;
        $this->toggleGoogleDrive = $toggleGoogleDrive;
        $this->toggleOneDrive = $toggleOneDrive;
        $this->cookieStorage = new CookieStore();
    }

    public function toggleCookieStore ( $toggle ) {
        if ( $toggle === false )
            $this->cookieStorage = null;
        else
            $this->cookieStorage = new CookieStore();
    }

    public function clearCookieStore () {
        if ( $this->cookieStorage )
            $this->cookieStorage->clear();
    }

    public function toggle ( $toggleDbx, $toggleOneDrive, $toggleGoogleDrive ) {
        $this->toggleDbx = $toggleDbx;
        $this->toggleGoogleDrive = $toggleGoogleDrive;
        $this->toggleOneDrive = $toggleOneDrive;
    }

    public function initDropbox ( $clientId, $clientSecret, $redirectUri ) {
        $this->dropboxDa = new DropboxDA($clientId, $clientSecret, $redirectUri);
        // if cookie storage is enabled, get token from there.
        if ( $this->cookieStorage )
            if ( $this->cookieStorage->platformHasToken( CookieStore::COOKIE_KEY_PLAT_DBX ) )
                $this->dropboxDa->setAccessToken( $this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_DBX ));
    }

    public function initGoogleDrive ( $clientId, $clientSecret, $redirectUri ) {
        $this->googleDriveDa = new GoogleDriveDA( $clientId, $clientSecret, $redirectUri );
        // if cookie storage is enabled, get token from there.
        if ( $this->cookieStorage )
            if ( $this->cookieStorage->platformHasToken( CookieStore::COOKIE_KEY_PLAT_GGL ) )
                $this->googleDriveDa->setAccessToken ( $this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_GGL ) );
    }

    public function initOneDrive ( $clientId, $clientSecret, $redirectUri ) {
        $this->oneDriveDa = new OneDriveDA( $clientId, $clientSecret, $redirectUri );
        // if cookie storage is enabled, get token from there.
        if ( $this->cookieStorage )
            if ( $this->cookieStorage->platformHasToken( CookieStore::COOKIE_KEY_PLAT_ONE_DRV ) )
                $this->oneDriveDa->setAccessToken ( $this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_ONE_DRV ) );
    }

    // add token.
    public function addToken ( $platform, $token, $isCode = false ) {
        if ( strcmp( self::PLATFORM_DROPBOX, $platform ) === 0 ) {
            // if dropbox is enabled.
            if ($this->toggleDbx === true) {
                $this->dropboxDa->setAccessToken($token, $isCode);
                if ($this->dropboxDa->getAccessToken()) {
                    if ($this->cookieStorage)
                        $this->cookieStorage->addToken(CookieStore::COOKIE_KEY_PLAT_DBX, $this->dropboxDa->getAccessToken());
                }
            }
        }
        else if ( strcmp( self::PLATFORM_GOOGLE_DRIVE, $platform ) === 0 ) {
            // if google drive is enabled.
            if ($this->toggleGoogleDrive === true) {
                $this->googleDriveDa->setAccessToken($token, $isCode);
                if ($this->cookieStorage)
                    $this->cookieStorage->addToken(CookieStore::COOKIE_KEY_PLAT_GGL, $this->googleDriveDa->getAccessToken());
            }
        }
        else if ( strcmp( self::PLATFORM_ONE_DRIVE, $platform ) === 0 ) {
            // if one drive is enabled.
            if ( $this->toggleOneDrive === true ) {
                echo "One drive enabled!";
                $this->oneDriveDa->setAccessToken ( $token, $isCode );
                if ( $this->cookieStorage )
                    $this->cookieStorage->addToken(CookieStore::COOKIE_KEY_PLAT_ONE_DRV, $this->oneDriveDa->getAccessToken());
            }
        }
    }

    // todo finish init onedrive, googledrive function,

    public function authorizeAll () {
        // check if dropbox is enabled.
        if ( $this->toggleDbx === true ) {
            if ( !$this->dropboxDa )
                die("Error: Dropbox not initialized!");
            $dbxToken = '';

            // check if cookie storage is enabled and contains dropbox key.
            if ( $this->cookieStorage ) {
                if ( strcmp( $this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_DBX ), '' ) !== 0 ) {
                    echo "<br /> Dropbox token found in cookie! ".$this->cookieStorage->getToken(CookieStore::COOKIE_KEY_PLAT_DBX)."<br />";
                    $this->dropboxDa->setAccessToken($this->cookieStorage->getToken(CookieStore::COOKIE_KEY_PLAT_DBX));
                }
                else
                    echo "<br /> Dropbox token not found in cookie.";
            }
            if ( strcmp( $dbxToken, '' ) === 0 )
                $dbxToken = $this->dropboxDa->getAccessToken();

            if ( strcmp( $dbxToken, '' ) === 0 )
                return $this->dropboxDa->getAuthUrl();
            else
                $this->dropboxDa->setAccessToken($dbxToken);
        }
        // if google drive is enabled.
        if ( $this->toggleGoogleDrive === true ) {
            if ( !$this->googleDriveDa )
                die ( "Error: Google drive not initialized!" );
            $gglToken = '';

            // check if cookie storage is enabled and contains google drive key.
            if ( $this->cookieStorage ) {
                if ( strcmp( $this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_GGL ), '' ) !== 0 ) {
                    echo "<br /> Google drive token found in cookie! ".$this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_GGL)."<br />";
                    $this->googleDriveDa->setAccessToken($this->cookieStorage->getToken(CookieStore::COOKIE_KEY_PLAT_GGL));
                }
                else
                    echo "<br /> Google drive token not found in cookie.";
            }
            if ( strcmp( $gglToken, '' ) === 0 )
                $gglToken = $this->googleDriveDa->getAccessToken();

            if ( strcmp( $gglToken, '' ) === 0 )
                return $this->googleDriveDa->getAuthUrl();
            else
                $this->googleDriveDa->setAccessToken($gglToken);
        }
        // if one drive is enabled.
        if ( $this->toggleOneDrive === true ) {
            if ( !$this->oneDriveDa )
                die ( "Error: One Drive drive not initialized!" );
            $onedToken = '';

            // check if cookie storage is enabled and contains one drive key.
            if ( $this->cookieStorage ) {
                if ( strcmp( $this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_ONE_DRV ), '' ) !== 0 ) {
                    echo "<br /> One drive token found in cookie! ".$this->cookieStorage->getToken( CookieStore::COOKIE_KEY_PLAT_ONE_DRV)."<br />";
                    $this->oneDriveDa->setAccessToken($this->cookieStorage->getToken(CookieStore::COOKIE_KEY_PLAT_ONE_DRV));
                }
                else
                    echo "<br /> One drive token not found in cookie.";
            }
            if ( strcmp( $onedToken, '' ) === 0 )
                $onedToken = $this->oneDriveDa->getAccessToken();

            if ( strcmp( $onedToken, '' ) === 0 )
                return $this->oneDriveDa->getAuthUrl();
            else
                $this->oneDriveDa->setAccessToken($onedToken);
        }
    }

    public function uploadFile( $localPath, $platfromPath )
    {
        if ( $this->toggleDbx === true ) {
            // upload file to dropbox.
            $this->dropboxDa->uploadFile ( $localPath, $platfromPath );
        }
        if ( $this->toggleGoogleDrive === true ) {
            // upload file to onedrive.
            $this->googleDriveDa->upload ( $localPath, $platfromPath );
        }
        if ( $this->toggleOneDrive === true ) {
            $this->oneDriveDa->upload ( $localPath, $platfromPath );
        }
    }

}

?>