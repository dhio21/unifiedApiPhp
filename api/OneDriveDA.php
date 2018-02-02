<?php
/**
 * Created by IntelliJ IDEA.
 * User: faisa
 * Date: 1/24/2018
 * Time: 8:16 PM
 */

namespace api;

class OneDriveDA {

    const API_URI_AUTH = "https://login.live.com/oauth20_authorize.srf";
    const API_URI_TOKEN = "https://login.live.com/oauth20_token.srf";
    const API_URI_UPLOAD = "https://apis.live.net/v5.0/me/skydrive/files%s";

    private $accessToken;

    private $clientId;
    private $clientSecret;
    private $redirectUri;

    function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function getAccessToken () {
        return $this->accessToken;
    }

    public function setAccessToken ( $token, $isCode = false ) {
        if ( $isCode === true ) {
            $token = $this->getAccessTokenFromCode( $token );
            if ( $token ) {
                $this->accessToken = $token;
            }
            else
                echo "<br /><hr /> Error! could not get access token!<br />";
        }
        else
            $this->accessToken = $token;
    }

    public function getAuthUrl () {
        $query = array(
            'client_id' => $this->clientId,
            'scope' => 'wl.signin wl.basic',
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri
        );
        return self::API_URI_AUTH."?".http_build_query($query);
        // return "https://login.live.com/oauth20_authorize.srf?client_id=8ca5ab63-ef75-4916-898d-076cc1a6d257&scope=wl.signin%20wl.basic&response_type= code&redirect_uri=http://localhost/UnifiedApi/one-drive-auth.php";
    }

    public function getAccessTokenFromCode ( $code ) {
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $query = array(
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        );

        $ch = curl_init( self::API_URI_TOKEN );

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_POST, true );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($query) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec( $ch );
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseJson = json_decode( $response );

        if ( strcmp( $httpCode, "200" ) === 0 ) {
            return $responseJson->access_token;
        }

        return null;
    }

    public function upload ( $localPath, $onDrvPath ) {

        $header = array(
            'Authorization: Bearer '.$this->accessToken
        );

        $uri = sprintf( self::API_URI_UPLOAD, $onDrvPath );
        $ch = curl_init( $uri );

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT" );

        $data = file_get_contents( $localPath );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $response = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        echo "<br /><hr /><br />Response : ".$response;
        echo "<br /><hr /><br />Http Status Code : ".$httpCode;

    }

}