<?php

use FtpClient\FtpClient;

/**
 * @group basic-ftp-tests
 */
class BasicFtpTest extends TestCase {
	private ?FtpClient $ftp = null;
	
	public function testCanConnectToFtpServer() {
		$this->makeFtp();
		
		$this->assertInstanceOf(FtpClient::class, $this->ftp);
		$this->assertTrue($this->ftp->isConnected());
	}
	
	private function makeFtp() {
		$creds = static::getBasicCreds();
		
		$this->ftp = new FtpClient();
		$this->ftp->setHost($creds['host']);
		$this->ftp->setPass($creds['pass']);
		$this->ftp->setUser($creds['user']);
		$this->ftp->setPort($creds['port']);
		$this->ftp->setPassiveMode($creds['pasv']);
		$this->ftp->setTimeout($creds['timeout']);
		$this->ftp->setUseSsh(false);
		
		$this->ftp->connect();
	}
}
