<?php
/**
 * Created by IntelliJ IDEA.
 * User: faisa
 * Date: 1/23/2018
 * Time: 7:11 PM
 */

namespace api;

class DropboxDA
{
    const API_URI_AUTHORIZE = "https://www.dropbox.com/oauth2/authorize";
    const API_URI_TOKEN = "https://api.dropboxapi.com/oauth2/token";
    const API_URI_UPLOAD_FILE = "https://content.dropboxapi.com/2/files/upload";

    private $token;
    private $clientId;
    private $clientSecret;
    private $redirectUri;

    function __construct($clientId, $clientSecret, $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function setAccessToken ( $tokenOrCode, $isCode = false ) {
        if ( $isCode === false )
            $this->token = $tokenOrCode;
        else
            $this->token = $this->getTokenFromCode( $tokenOrCode );
    }

    public function getAccessToken ( ) {
        return $this->token;
    }

    public function getAuthUrl () {
        $query = array(
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri
        );

        return self::API_URI_AUTHORIZE."?".http_build_query($query);
    }

    public function getTokenFromCode ( $code ) {
        $postBody = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri
        );

        $ch = curl_init(self::API_URI_TOKEN);

        curl_setopt($ch, CURLOPT_HEADER, "Content-Type: application/x-www-form-urlencoded");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postBody));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseJson = json_decode(curl_exec( $ch ));
        curl_close($ch);
        return $responseJson->access_token;
    }

    public function uploadFile ( $pathLocal, $pathDbx ) {
        $headers = array('Authorization: Bearer '. $this->token,
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: '.
            json_encode(
                array(
                    "path"=> $pathDbx,
                    "mode" => "add",
                    "autorename" => true,
                    "mute" => false
                )
            )

        );

        $ch = curl_init(self::API_URI_UPLOAD_FILE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);

        $fp = fopen($pathLocal, 'rb');
        $fileSize = filesize($pathLocal);

        curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fp, $fileSize));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo($response.'<br/>');
        echo($http_code.'<br/>');

        curl_close($ch);
    }

}