<?php
/**
 * Created by IntelliJ IDEA.
 * User: faisa
 * Date: 1/22/2018
 * Time: 8:01 PM
 */

namespace api\dto;


class UApiToken {

    private $id;
    private $dropbox;
    private $googleDrive;
    private $oneDrive;

    function __construct($id = 0, $dropbox = null, $googleDrive = null, $oneDrive = null) {
        $this->id = $id;
        $this->dropbox = $dropbox;
        $this->googleDrive = $googleDrive;
        $this->oneDrive = $oneDrive;
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
     * @return mixed
     */
    public function getDropbox()
    {
        return $this->dropbox;
    }

    /**
     * @param mixed $dropbox
     */
    public function setDropbox($dropbox)
    {
        $this->dropbox = $dropbox;
    }

    /**
     * @return mixed
     */
    public function getGoogleDrive()
    {
        return $this->googleDrive;
    }

    /**
     * @param mixed $googleDrive
     */
    public function setGoogleDrive($googleDrive)
    {
        $this->googleDrive = $googleDrive;
    }

    /**
     * @return mixed
     */
    public function getOneDrive()
    {
        return $this->oneDrive;
    }

    /**
     * @param mixed $oneDrive
     */
    public function setOneDrive($oneDrive)
    {
        $this->oneDrive = $oneDrive;
    }



}