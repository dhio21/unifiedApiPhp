<?php
/**
 * Created by IntelliJ IDEA.
 * User: faisa
 * Date: 1/24/2018
 * Time: 2:09 PM
 */

namespace api;

require_once("vendor/autoload.php");

use \Google_Client as gcl;

class GoogleDriveDA
{

    const API_URI_OAUTH2 = "https://accounts.google.com/o/oauth2/v2/auth";
    const API_URI_TOKEN  = "https://www.googleapis.com/oauth2/v4/token";
    const API_URI_UPLOAD = "https://www.googleapis.com/upload/drive/v2/files/%s?uploadType=media";
    const API_URI_CREATE = "https://www.googleapis.com/drive/v2/files";

    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $accessToken;

    function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function setAccessToken($tokenOrCode, $isCode = false)
    {
        if ($isCode === false)
            $this->accessToken = $tokenOrCode;
        else
            $this->accessToken = $this->getAccessTokenFromCode($tokenOrCode);
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getAuthUrl()
    {
        $query = array(
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'https://www.googleapis.com/auth/drive',
            'response_type' => 'code',
            'access_type' => 'offline',
            'include_granted_scope' => 'true',
            'state' => 'state_parameter_passthrough_value'
        );
        return self::API_URI_OAUTH2 . "?"
        . http_build_query($query);
    }

    public function getAccessTokenFromCode($code) {
        $body = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $code
        );

        $ch = curl_init(self::API_URI_TOKEN);

        curl_setopt($ch, CURLOPT_HEADER, "Content-Type: application/x-www-form-urlencoded");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseJson = json_decode(curl_exec($ch));
        curl_close($ch);

        echo "<br />Access Token : " . json_encode($responseJson);

        return $responseJson->access_token;
    }

    public function create ( $name, $path, $description="" ) {
        $reqBody = array(
            'title' => $name,
            'mimeType' => mime_content_type($path),
            'description' => $description
        );

        $reqHeaders = array(
            'Authorization: Bearer '.$this->accessToken,
            'Content-Type: application/json'
        );

        $ch = curl_init(self::API_URI_CREATE);

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $reqHeaders );
        curl_setopt($ch, CURLOPT_POST, true );

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($reqBody) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $respJson = json_decode($response);

        if ( strcmp( $http_code, "200" ) === 0 )
            return $respJson->id;
        else
            echo "Create Error : ".$response;
        return null;
    }

    public function upload($localPath, $gglDrvPath)
    {
        $id = $this->create( basename($gglDrvPath), $localPath );

        if ( $id ) {
            $fileSize = filesize($localPath);
            $mimeType = mime_content_type($localPath);

            $headers = array(
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: ' . $mimeType,
                'Content-Length: ' . $fileSize
            );

            $ch = curl_init( sprintf( self::API_URI_UPLOAD, $id ));

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

            $file_content = file_get_contents($localPath);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $file_content);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//            echo($response . '<br/>');
//            echo($http_code . '<br/>');

            if ( strcmp( $http_code, "200" ) !== 0 )
                echo "Error : ".$response;

            curl_close($ch);
        }
    }

}