<?php

if (is_dir(__DIR__ . '/vendor')) {
	require_once(__DIR__ . '/vendor/autoload.php');
}

spl_autoload_register(function ($className) {
	
	if (strpos($className, 'FtpClient\\') !== 0) {
		return;
	}
	
	$normalizedClass = str_replace(['FtpClient\\', '\\'], ['', '/'], $className);
	$file = __DIR__ . '/src/' . $normalizedClass . '.php';
	
	if (file_exists($file)) {
		require_once($file);
	}
	
});
	