<?php
namespace api;

require "UapiConfiguration.php";
require "dto/UApiToken.php";
require "AesHelper.php";

use mysqli;

class DbConnection
{
    const TABLE_USER = "uapi_user";
    const TABLE_TOKEN = "uapi_token";

    const COL_ID = "id";
    const COL_NAME = "name";
    const COL_PWD = "pwd";
    const COL_DROPBOX = "dbx";
    const COL_GOOGLE_DRIVE = "google";
    const COL_ONE_DRIVE = "one_drive";
    const COL_REF_USER = "user_ref";

    const QUERY_FOR_FIND_TABLE_USER = "SHOW TABLES LIKE '" . self::TABLE_USER . "'";
    const QUERY_FOR_FIND_TABLE_TOKEN = "SHOW TABLES LIKE '" . self::TABLE_TOKEN . "'";

    const QUERY_FOR_CREATE_TABLE_USER = "CREATE TABLE " . self::TABLE_USER . " ("
        . " " . self::COL_ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT, " . " " . self::COL_NAME . " VARCHAR(50) NOT NULL UNIQUE ,"
        . " " . self::COL_PWD . " VARCHAR(10) NOT NULL" . ");";
    const QUERY_FOR_CREATE_TABLE_TOKEN = "CREATE TABLE " . self::TABLE_TOKEN . " ("
        . " " . self::COL_ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT, " . self::COL_DROPBOX . " VARCHAR(255),"
        . " " . self::COL_GOOGLE_DRIVE . " VARCHAR(255) , " . self::COL_ONE_DRIVE . " VARCHAR(50),"
        . " " . self::COL_REF_USER . " INT NOT NULL UNIQUE,"
        . " FOREIGN KEY (" . self::COL_REF_USER . ") REFERENCES " . self::TABLE_USER . "(" . self::COL_ID . ")"
    . ");";

    private $connection;
    private $aesHelper;

    function __construct() {
        // init the aes helper.
        $this->aesHelper = new AesHelper();

        // open a new connection from the config object.
        $databaseConfigs = (new UapiConfiguration())->getDatabase();

        $this->connection = new mysqli(
            $databaseConfigs->getHost(),
            $databaseConfigs->getUser(),
            $databaseConfigs->getPwd(),
            $databaseConfigs->getName());

        // create user table if it does not exists.
        if (mysqli_num_rows(mysqli_query($this->connection, self::QUERY_FOR_FIND_TABLE_USER)) != 1) {
            if (!mysqli_query($this->connection, self::QUERY_FOR_CREATE_TABLE_USER))
                echo "error creating user table. SQL : " . self::QUERY_FOR_CREATE_TABLE_USER;
        }
        // create token table if it does not exists.
        if (mysqli_num_rows(mysqli_query($this->connection, self::QUERY_FOR_FIND_TABLE_TOKEN)) != 1) {
            if (!mysqli_query($this->connection, self::QUERY_FOR_CREATE_TABLE_TOKEN))
                echo "error creating user table. SQL : " . self::QUERY_FOR_CREATE_TABLE_USER;
        }
    }

    function __destruct() {
        if (!$this->connection->close())
            echo "Could not close mysql connection!";
    }

    public function isUser ( $userName, $password ) {
        $query = "SELECT * FROM user WHERE " . self::COL_NAME . " = " . $userName . " AND " . self::COL_PWD . " = " . $password;
        if (mysqli_query($this->connection, $query)->num_rows > 0)
            return true;
        return false;
    }

    public function getUser( $userName, $password ) {
        $query = "SELECT * FROM ".self::TABLE_USER." WHERE " . self::COL_NAME . " = '" . $userName . "' AND " . self::COL_PWD . " = '" . $password."'";
        if ($result = mysqli_query($this->connection, $query)) {
            $recordArr = $result->fetch_array();
            //print_r($recordArr);
            return new dto\UApiUser($recordArr["id"], $recordArr["name"], $recordArr["pwd"]);
        }
        return null;
    }

    public function addUser( $userName, $password ) {
        $query = "INSERT INTO " . self::TABLE_USER
            . " ( " . self::COL_NAME . " , " . self::COL_PWD . " )"
            ." VALUES "
            . " ( '" . $userName . "' , '" . $password . "' ); ";
        mysqli_query($this->connection, $query);
        return $this->getUser($userName, $password);
    }

    public function getTokensFor( $userId ) {
        if ($userId === 0)
            return null;

        $query = "SELECT * FROM " . self::TABLE_TOKEN . " WHERE " . self::COL_REF_USER . " = " . $userId;
        if ($result = mysqli_query($this->connection, $query)) {
            if ( $result -> num_rows <= 0 )
                return null;
            $record = $result->fetch_array();
            echo "DBX Enco type : ".mb_detect_encoding($this->aesHelper->decrypt($record[self::COL_DROPBOX]));
            return new dto\UApiToken(
                $record[self::COL_ID],
                $this->aesHelper->decrypt($record[self::COL_DROPBOX]),
                $this->aesHelper->decrypt($record[self::COL_GOOGLE_DRIVE]),
                $this->aesHelper->decrypt($record[self::COL_ONE_DRIVE]));
        }
        return null;
    }

    public function addTokenFor ( $userId, $platform, $token ) {
        $col = "";
        $vals = "";
        $encToken = $this->aesHelper->encrypt($token);
        if ( strcmp( "dbx" , $platform) === 0 || strcmp( "dropbox" , $platform) === 0 ) {
            $col = self::COL_DROPBOX;
            $vals = "( '".$encToken."' , '' , '' , ".$userId." )";
        }
        else if ( strcmp( "googleDrive", $platform ) === 0 || strcmp( "ggl", $platform ) === 0 ) {
            $col = self::COL_GOOGLE_DRIVE;
            $vals = "( '', '', '".$encToken."' , ".$userId." )";
        }
        else if ( strcmp( "oneDrive", $platform ) === 0 || strcmp( "oned", $platform ) === 0 ) {
            $col = self::COL_ONE_DRIVE;
            $vals = "( '', '".$encToken."', '' , ".$userId." )";
        }

        if ( $this->getTokensFor( $userId ) ) {
            $query = "UPDATE ".self::TABLE_TOKEN." SET ".$col." = '".$encToken."' WHERE ".self::COL_REF_USER." = ".$userId;
            if ($result = mysqli_query($this->connection, $query)) {
                if ( mysqli_affected_rows($this->connection) <= 0 )
                    return false;
                return true;
            }
        }
        else {
            $query = "INSERT INTO ".self::TABLE_TOKEN
                        ."(".self::COL_DROPBOX." , ".self::COL_ONE_DRIVE." , ".self::COL_GOOGLE_DRIVE.", ".self::COL_REF_USER." )"
                        ." VALUES "
                        .$vals;
            echo "<br /> Query : ".$query." <br />";
            if ( $result = mysqli_query($this->connection, $query) ) {
                if ( mysqli_affected_rows( $this->connection ) <= 0 )
                    return false;
                return true;
            }
        }
    }

}

?>