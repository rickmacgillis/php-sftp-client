<?php

class Credentials {
	public static array $sftpCredentials = [
		"host" => "example.com",
		"user" => "testuser",
		"pass" => "testpass",
		"port" => 22,
		'timeout' => 30,
		"pasv" => false,
	];
	
	public static array $basicCredentials = [
		"host" => "example.com",
		"user" => "testuser",
		"pass" => "testpass",
		"port" => 21,
		'timeout' => 30,
		"pasv" => true,
	];
}
