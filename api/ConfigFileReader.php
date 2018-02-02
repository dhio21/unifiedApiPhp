<?php
namespace api;

require "Database.php";

ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0');

const CONFIG_FILE_NAME = "UapiConfig.xml";

class ConfigFileReader
{

    function __construct(){}

    function __destruct(){}


    public function readDbConfigs()
    {
        if (file_exists(CONFIG_FILE_NAME)) {
            $file_data = simplexml_load_file(CONFIG_FILE_NAME);
            $database = new Database();

            $database->setHost( $file_data->UapiDb->UapiHost );
            $database->setPort( isset($file_data->UapiDb->UapiPort) ? $file_data->UapiDb->UapiPort : '3306');
            $database->setName( $file_data->UapiDb->UapiDb );
            $database->setUser( $file_data->UapiDb->UapiUser );
            $database->setPwd ( $file_data->UapiDb->UapiPwd );

            return $database;
        }
        echo " Config file not found! ";
        return null;
    }


}

?>