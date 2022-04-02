<?php

use FtpClient\FtpClient;

require_once(__DIR__ . '/FtpFunctionsTestAbstract.php');

/**
 * @group basic-ftp-tests
 */
class BasicFtpTest extends FtpFunctionsTestAbstract {
	protected function makeFtp() {
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
