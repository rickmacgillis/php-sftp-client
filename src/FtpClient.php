<?php

namespace FtpClient;

use FtpClient\Basic\BasicFtpClient;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class FtpClient {
	private $ftp = null;
	
	private ?string $host = null;
	private ?string $user = null;
	private ?string $pass = null;
	private ?string $privKey = null;
	private ?string $pubKey = null;
	private bool $isSsh = false;
	private bool $pasv = false;
	private int $port = 21;
	private int $timeout = 90;
	
	private ?SFTP $sftpClient = null;
	private ?BasicFtpClient $basicClient = null;
	
	public function setHost(string $host) {
		$this->host = $host;
	}
	
	public function setUseSsh(bool $useSsh) {
		$this->isSsh = $useSsh;
	}
	
	public function setPassiveMode(bool $pasv) {
		$this->pasv = $pasv;
	}
	
	public function setPort(int $port) {
		$this->port = $port;
	}
	
	public function setTimeout(int $timeoutSeconds) {
		$this->timeout = $timeoutSeconds;
	}
	
	public function setUser(string $username) {
		$this->user = $username;
	}
	
	public function setPass(string $password) {
		$this->pass = $password;
	}
	
	public function setPubKey(string $pubKey) {
		$this->pubKey = $pubKey;
	}
	
	public function setPrivKey(string $privKey) {
		$this->privKey = $privKey;
	}
	
	public function connect() {
		if ($this->isSsh) {
			$this->setSshHandler();
		} else {
			$this->setBasicHandler();
		}
	}
	
	public function isConnected() {
		if (!is_null($this->basicClient)) {
			return is_resource($this->basicClient->getConnection());
		}
		
		if (!is_null($this->sftpClient)) {
			return $this->sftpClient->isConnected();
		}
		
		return false;
	}
	
	public function __call(string $method, array $args) {
		if ($this->isSsh) {
			return call_user_func_array([$this->sftpClient, $method], $args);
		} else {
			return call_user_func_array([$this->basicClient, $method], $args);
		}
	}
	
	private function setSshHandler() {
		$this->sftpClient = new SFTP($this->host, $this->port, $this->timeout);
		
		$password = $this->pass;
		if (!empty($this->privKey)) {
			$keyPass = empty($this->pass) ? false : $this->pass;
			$password = PublicKeyLoader::loadPrivateKey($this->privKey, $keyPass);
		} else if (!empty($this->pubKey)) {
			$password = PublicKeyLoader::loadPublicKey($this->pubKey);
		}
		
		$this->sftpClient->login($this->user, $password);
	}
	
	private function setBasicHandler() {
		$this->basicClient = new BasicFtpClient();
		$this->basicClient->connect($this->host, false, $this->port, $this->timeout);
		$this->basicClient->login($this->user, $this->pass);
		$this->basicClient->pasv($this->pasv);
	}
}
