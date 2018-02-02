<?php

$dropboxApiId = 'ba90hh6hmg8g2to';
$dropboxApiSecret = 'f40q7lnr4waczgx';
$dropboxRedirectUri = 'http://localhost/UnifiedApi/dbx-auth.php';

require_once("./api/UApi.php");
$uApi = new api\UApi(true, true, true);
$uApi->initDropbox($dropboxApiId, $dropboxApiSecret, $dropboxRedirectUri);

if (isset($_GET["code"])) {
    $uApi->addToken(\api\UApi::PLATFORM_DROPBOX, $_GET["code"], true);
    //echo "<br/> Token in cookie : ".$_COOKIE[\api\CookieStore::COOKIE_APP_NAME];
}

?>