<?php
$oneDrvApiId = '8ca5ab63-ef75-4916-898d-076cc1a6d257';
$oneDrvApiSec = 'eblocfTEAK593[lBIZ98^$*';
$oneDrvRedirectUri = 'http://localhost/UnifiedApi/one-drive-auth.php';

require_once("./api/UApi.php");
$uApi = new api\UApi(false, true, false);
$uApi->initOneDrive( $oneDrvApiId, $oneDrvApiSec, $oneDrvRedirectUri );

if (isset($_GET["code"])) {
    $uApi->addToken(\api\UApi::PLATFORM_ONE_DRIVE, $_GET["code"], true);
}
?>
