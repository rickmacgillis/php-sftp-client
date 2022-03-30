<?php

namespace FtpClient;

use FtpClient\Ssh\Password;
use FtpClient\Ssh\Key;
use FtpClient\Ssh\SFTP;
use FtpClient\Basic\BasicFtpClient;

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
	
	public function setPubKey(string $username) {
		$this->user = $username;
	}
	
	public function setPrivKey(string $password) {
		$this->pass = $password;
	}
	
	public function connect() {
		if ($this->isSsh) {
			$this->setSshHandler();
		} else {
			$this->setBasicHandler();
		}
	}
	
	public function __call(string $method, array $args) {
		if ($this->isSsh) {
			return call_user_func_array([$this->sftpClient, $method], $args);
		} else {
			return call_user_func_array([$this->basicClient, $method], $args);
		}
	}
	
	private function setSshHandler() {
		if (!empty($this->pass)) {
			$auth = new Password($this->user, $this->pass);
		} else {
			$auth = new Key($this->user, $this->pubKey, $this->privKey);
		}
		
		$this->sftpClient = new SFTP($this->host, $auth, $this->port);
	}
	
	private function setBasicHandler() {
		$this->basicClient = new BasicFtpClient();
		$this->basicClient->connect($this->host, false, $this->port, $this->timeout);
		$this->basicClient->login($this->user, $this->pass);
		$this->basicClient->pasv($this->pasv);
	}
}
