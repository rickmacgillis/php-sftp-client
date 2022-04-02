<?php

use FtpClient\FtpClient;

abstract class FtpFunctionsTestAbstract extends TestCase {
	protected ?FtpClient $ftp = null;
	
	public function setUp() : void {
		$this->makeFtp();
	}
	
	public function testCanConnectToFtpServer() {
		$this->assertInstanceOf(FtpClient::class, $this->ftp);
		$this->assertTrue($this->ftp->isConnected());
	}
	
	public function testCanMakeADirectory() {
		$success = $this->ftp->mkdir('/test');
		$this->assertTrue($success);
	}
	
	public function testCanCheckIfSomethingIsADirectory() {
		$success = $this->ftp->isDir('/test');
		$this->assertTrue($success);
		
		$success = $this->ftp->isDir('/fake-dir');
		$this->assertFalse($success);
	}
	
	public function testCanRemoveADirectory() {
		$success = $this->ftp->rmdir('/test');
		$this->assertTrue($success);
	}
	
	public function testCanCreateAndReadAFile() {
		$file = '/test-file.txt';
		$content = 'just a test';
		$data = $this->ftp->getContent($file);
		$this->assertEmpty($data);
		
		$this->ftp->putFromString($file, $content);
		$data = $this->ftp->getContent($file);
		$this->assertSame($content, $data);
	}
	
	public function testCanGetFileModifiedTime() {
		$file = '/test-file.txt';
		$mtime = $this->ftp->modifiedTime($file);
		$this->assertIsInt($mtime);
	}
	
	public function testCanGetFileSize() {
		$file = '/test-file.txt';
		$size = $this->ftp->size($file);
		$this->assertTrue($size > 0);
	}
	
	public function testCanRenameFile() {
		$origFilename = '/test-file.txt';
		$newFilename = '/test-file-renamed.txt';
		$content = 'just a test';
		
		$this->ftp->rename($origFilename, $newFilename);
		$this->assertEmpty($this->ftp->getContent($origFilename));
		$this->assertSame($content, $this->ftp->getContent($newFilename));
	}
	
	public function testCanRemoveAFile() {
		$file = '/test-file-renamed.txt';
		
		$this->ftp->delete($file);
		$this->assertEmpty($this->ftp->getContent($file));
	}
	
	public function testCanScanDirectory() {
		$this->ftp->mkdir('/test');
		$this->ftp->mkdir('/test/blah');
		$this->ftp->putFromString('/test/file1.txt', 'test file 1');
		$this->ftp->putFromString('/test/file2.txt', 'test file 2');
		
		$out = $this->ftp->scanDir('/test');
		$this->assertCount(3, $out);
		
		$this->ftp->delete('/test/file1.txt');
		$this->ftp->delete('/test/file2.txt');
		$this->ftp->rmdir('/test/blah');
		$this->ftp->rmdir('/test');
	}
	
	abstract protected function makeFtp();
}
