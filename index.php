<?php
$dropboxApiId = 'ba90hh6hmg8g2to';
$dropboxApiSecret = 'f40q7lnr4waczgx';
$dropboxRedirectUri = 'http://localhost/UnifiedApi/dbx-auth.php';

$googleDApiId = '461226492715-oosa74tock8ba4tiftqo32bq60sumbns.apps.googleusercontent.com';
$googleDApiSec = 'Z1nrvuuT62kMpX5kxAdcN85K';
$googleDRedirectUri = 'http://localhost/UnifiedApi/ggl-auth.php';

$oneDrvApiId = '8ca5ab63-ef75-4916-898d-076cc1a6d257';
$oneDrvApiSec = 'eblocfTEAK593[lBIZ98^$*';
$oneDrvRedirectUri = 'http://localhost/UnifiedApi/one-drive-auth.php';

require_once("./api/UApi.php");
$uApi = new api\UApi( true, true, true );

// init dropbox.
$uApi->initDropbox( $dropboxApiId, $dropboxApiSecret, $dropboxRedirectUri );
$uApi->initGoogleDrive( $googleDApiId, $googleDApiSec, $googleDRedirectUri );
$uApi->initOneDrive( $oneDrvApiId, $oneDrvApiSec, $oneDrvRedirectUri );

if ( $authUrl = $uApi->authorizeAll() ) {
    header( 'Location: '.$authUrl );
}

if ( isset( $_POST["file"] ) ) {
    $localPath = $_POST["file"];
    $platPath = isset($_POST["pathPlatfrm"]) ? $_POST["pathPlatfrm"] : basename( $_POST["file"] );

    echo "<hr /><br/> Local Path : ".$_POST["file"];
    echo "<br/> Platform Path : ".$platPath;

    $uApi->uploadFile( $localPath, $platPath);
}

?>
<!Doctype html>
<html>
<head>
    <title>Unified Api Demo</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>

    <form method="post" action="index.php">
        <input type="text" name="pathPlatfrm" placeholder="Path and name on platform." />
        <input type="file" name="file" placeholder="Select file to upload."/>
        <ul>
            <li><input type="checkbox" name="dbx_toggle" checked/><label for="dbx_toggle"> Dropbox.</label></li>
            <li><input type="checkbox" name="ggl_toggle" checked/><label for="ggl_toggle"> Google Drive.</label></li>
            <li><input type="checkbox" name="odrv_toggle" checked/><label for="odrv_toggle"> One Drive.</label></li>
        </ul>
        <input type="submit" value="upload" />
    </form>

</body>
</html>