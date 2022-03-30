<?php

use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCaseFixtureNotFoundException extends \Exception {};

class TestCase extends PhpUnitTestCase {
	protected static function getFixture($name) {
		$file = realpath(__DIR__ . '/fixtures/' . $name);
		if (file_exists($file)) {
			return file_get_contents($file);
		}
		
		throw new TestCaseFixtureNotFoundException($name);
	}
	
	protected static function getSftpCreds() {
		$this->throwIfMissingCredentials();
		return Credentials::$sftpCredentials;
	}
	
	protected static function getBasicCreds() {
		$this->throwIfMissingCredentials();
		return Credentials::$basicCredentials;
	}
	
	private static function throwIfMissingCredentials() {
		if (class_exists('Credentials') === false) {
			throw new Exception(
				"You must rename CredentialsSample.php to Credentials.php in " .
				"/tests and populate it with your FTP credentials before " .
				"running the tests."
			);
		}
	}
}
