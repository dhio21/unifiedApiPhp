<?php
/**
 * Created by IntelliJ IDEA.
 * User: faisa
 * Date: 1/22/2018
 * Time: 5:53 PM
 */

namespace api\dto;

class UApiUser {

    private $id;
    private $userName;
    private $password;

    function __construct( $id = 0, $userName = "" , $password = "")
    {
        $this->id = $id;
        if ( strcmp( $userName, "") != 0 )
            $this->userName = $userName;
        if ( strcmp( $password, "" ) != 0 )
            $this->password = $password;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }



}