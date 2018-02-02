<?php
namespace api;

require "ConfigFileReader.php";

class UapiConfiguration
{
    private $database;

    function __construct(){
        //load database configurations from file reader
        $this->database = (new ConfigFileReader())->readDbConfigs();
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param mixed $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

}
?>