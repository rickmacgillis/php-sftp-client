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
	
	public function mkdir(string $dir) : bool {
		return $this->ftpCall('mkdir', 'mkdir', [
			$dir
		]);
	}
	
	public function isDir(string $item) : bool {
		return $this->ftpCall('is_dir', 'isDir', [
			$item
		]);
	}
	
	public function rmdir(string $dir) : bool {
		return $this->ftpCall('delete', 'rmdir', [
			$dir
		]);
	}
	
	public function scanDir(string $dir) {
		if ($this->isSsh) {
			return $this->normalizeScanDir($this->sftpClient->rawlist($dir));
		}
		
		return $this->basicClient->scanDir($dir);
	}
	
	public function putFromString(string $filename, string $contents) {
		return $this->ftpCall('put', 'putFromString', [
			$filename, $contents
		]);
	}
	
	public function getContent(string $filename) {
		$out = $this->ftpCall('get', 'getContent', [
			$filename
		]);
		
		return is_null($out) ? false : $out;
	}
	
	public function modifiedTime(string $filename) : int {
		return $this->ftpCall('filemtime', 'modifiedTime', [
			$filename
		]);
	}
	
	public function size(string $filename) : int {
		return $this->ftpCall('filesize', 'size', [
			$filename
		]);
	}
	
	public function rename(string $origFile, string $targetFile) : bool {
		return $this->ftpCall('rename', 'rename', [
			$origFile, $targetFile
		]);
	}
	
	public function delete(string $filename) : bool {
		return $this->ftpCall('delete', 'delete', [
			$filename
		]);
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
	
	private function ftpCall(string $sftpMethod, string $ftpMethod, array $args) {
		if ($this->isSsh) {
			return call_user_func_array([$this->sftpClient, $sftpMethod], $args);
		}
		
		return call_user_func_array([$this->basicClient, $ftpMethod], $args);
	}
	
	private function normalizeScanDir(array $output) : array {
		$out = [];
		foreach ($output as $filename => $filedata) {
			$out[$filename] = [
				'size'			=> $filedata['size'],
				'month'			=> date("M", $filedata['mtime']),
				'day'			=> date("d", $filedata['mtime']),
				'time'			=> date('H:i', $filedata['mtime']),
				'name'			=> $filedata['filename'],
				'type'			=> $filedata['type'] === NET_SFTP_TYPE_DIRECTORY ? 'directory' : 'file',
				'target'		=> null,
			];
		}
		
		return $out;
	}
}
