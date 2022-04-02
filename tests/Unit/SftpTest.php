<?php

use FtpClient\FtpClient;

require_once(__DIR__ . '/FtpFunctionsTestAbstract.php');

/**
 * @group sftp-tests
 */
class SftpTest extends FtpFunctionsTestAbstract {
	protected function makeFtp() {
		$creds = static::getSftpCreds();
		
		$this->ftp = new FtpClient();
		$this->ftp->setHost($creds['host']);
		$this->ftp->setPass($creds['pass']);
		$this->ftp->setUser($creds['user']);
		$this->ftp->setPort($creds['port']);
		$this->ftp->setTimeout($creds['timeout']);
		$this->ftp->setUseSsh(true);
		
		$this->ftp->connect();
	}
}
