<?php

namespace FtpClient\Ssh;

use FtpClient\FtpException;

class SFTP extends SSH2 {
    protected $sftp;

    public function __construct($host, Authentication $auth, $port = 22) {
        parent::__construct($host, $auth, $port);
        if ($this->isConnected()) {
            $this->sftp = ssh2_sftp($this->conn);
        } else {
        	throw new FtpException('SFTP Not Connected to host');
        }

    }
    public function __call($func, $args) {
        if (!$this->isConnected()) return false;
        $func = 'ssh2_sftp_' . $func;
        if (function_exists($func)) {
            array_unshift($args, $this->sftp);
            return call_user_func_array($func, $args);
        } else {
        	throw new FtpException($func . ' is not a valid SFTP function');
        }
    }

    public function list($path, $maxFiles = 0) {
    	if (!$this->isConnected()) return false;
        $result = array();
        $files = $this->ls($path);
        if (!empty($files)) {
            $totalFiles = 0;
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                	if ($maxFiles > 0 && $totalFiles >= $maxFiles) break;
                    $result[] = $path . '/' . $file;
                    $totalFiles++;
                }
            }
        }
        return $result;
    }

    public function ls($path) {
    	return scandir('ssh2.sftp://' . $this->sftp . '/' . $path);
    }

    public function mv($sourceFile, $destFile, $renameExistingFiles = true) {
        if (!$this->isConnected()) return false;

        if ($renameExistingFiles) {
        	$destFile = $this->uniqueNameFor($destFile);
        }

        return $this->rename($sourceFile, $destFile);
    }

    public function rm($remoteFile) {
    	return unlink('ssh2.sftp://' . $this->sftp . '/' . $remoteFile);
    }

    public function get($localFile, $remoteFile) {
        if (!$this->isConnected()) return false;
        
        $data = file_get_contents('ssh2.sftp://' . $this->sftp . '/' . $remoteFile);
        return file_put_contents($localFile, $data);
    }

    public function put($remoteFile, $localFile, $renameExistingFiles = true) {
        if (!$this->isConnected()) return false;

        if ($renameExistingFiles) {
        	$remoteFile = $this->uniqueNameFor($remoteFile);
        }

        if ($stream = fopen('ssh2.sftp://' . $this->sftp . '/' . $remoteFile, 'w')) {
        	$data = file_get_contents($localFile);

            if (fwrite($stream, $data)) {
                fclose($stream);
                return true;
            }
        }
        
        return false;
    }

    public function isDir($remoteFile) {
    	return is_dir('ssh2.sftp://' . $this->sftp . '/' . $remoteFile);
    }

    public function exists($remoteFile) {
    	return file_exists('ssh2.sftp://' . $this->sftp . '/' . $remoteFile);
    }
    
    private function uniqueNameFor($remoteFile) {
    	$pathInfo = pathinfo($remoteFile);
    	
    	$targetName = $pathInfo['filename'];
    	$extension = $pathInfo['extension'];
    	$dirname = $pathInfo['dirname'];
    	
    	$i = 1;
    	while($this->exists($remoteFile)) {
    		$targetName .= '(' . $i . ')';
    		$remoteFile = $dirname . '/' . $targetName . '.' . $extension;
    		$i++;
    	}
    	
    	return $remoteFile;
    }
}
