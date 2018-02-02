<?php
/**
 * Created by IntelliJ IDEA.
 * User: faisa
 * Date: 1/26/2018
 * Time: 1:53 PM
 */

namespace api;

require "vendor/autoload.php";

use PhpAes\Aes;

class AesHelper {

    private $key;
    private $iv;
    private $type;

    private $aes;

    function __construct() {
        $this->iv = "911154sherlocked";
        $this->key = "sherlocked911154";
        $this->type = "CBC";

        $this->aes = new Aes($this->key, $this->type, $this->iv );
    }

    /**
     * Encrypt the given text.
     * @param $text the text to encrypt.
     * @return \PhpAes\ciphertext cipher text.
     */
    public function encrypt ( $text ) {
        return $this->aes->encrypt( $text );
    }

    /**
     * Decrypt the given cipher text.
     * @param $cipher the cipher text.
     * @return mixed decrypted cipher text.
     */
    public function decrypt ( $cipher ) {
        return $this->aes->decrypt( $cipher );
    }

}