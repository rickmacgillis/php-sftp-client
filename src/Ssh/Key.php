<?php

namespace FtpClient\Ssh;

class Key extends Authentication {
    protected $username;
    protected $publicKey;
    protected $privateKey;

    public function __construct($username, $publicKey, $privateKey) {
        $this->username = $username;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPublicKey() {
        return $this->publicKey;
    }

    public function getPrivateKey() {
        return $this->privateKey;
    }
}