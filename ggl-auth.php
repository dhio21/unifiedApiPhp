<?php
$googleDApiId = '461226492715-oosa74tock8ba4tiftqo32bq60sumbns.apps.googleusercontent.com';
$googleDApiSec = 'Z1nrvuuT62kMpX5kxAdcN85K';
$googleDRedirectUri = 'http://localhost/UnifiedApi/ggl-auth.php';

require_once("./api/UApi.php");
$uApi = new api\UApi(false, true, true);
$uApi->initGoogleDrive($googleDApiId, $googleDApiSec, $googleDRedirectUri);

if (isset($_GET["code"])) {
    $uApi->addToken(\api\UApi::PLATFORM_GOOGLE_DRIVE, $_GET["code"], true);
    //echo "<br/> Token in cookie : ".$_COOKIE[\api\CookieStore::COOKIE_APP_NAME];
}
?>
