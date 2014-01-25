<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use Derhansen\Quixr\Helper\FilesystemHelper;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamPrintVisitor;

class FilesystemHelperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfNoPathGivenTest() {
		$helper = new FilesystemHelper();
		$this->assertFalse($helper->getSubdirectories(NULL));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfEmptyPathGivenTest() {
		$helper = new FilesystemHelper();
		$this->assertFalse($helper->getSubdirectories(''));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfPathDoesNotEndWithSlashTest() {
		$helper = new FilesystemHelper();
		$this->assertFalse($helper->getSubdirectories('/var/www'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfNonExistingPathGivenTest() {
		$helper = new FilesystemHelper();

		vfsStream::setup('var');
		$this->assertFalse($helper->getSubdirectories(vfsStream::url('var/www/')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsArrayOfDirsForExistingPathTest() {
		$helper = new FilesystemHelper();

		$root = vfsStream::setup('var');
		$wwwDir = vfsStream::newDirectory('www', 777);
		$wwwDir->addChild(vfsStream::newDirectory('vhost1', 777));
		$wwwDir->addChild(vfsStream::newDirectory('vhost2', 777));
		$wwwDir->addChild(vfsStream::newDirectory('vhost3', 777));
		$wwwDir->addChild(vfsStream::newFile('testfile.txt', 777));
		$root->addChild($wwwDir);

		$expected = array('vhost1', 'vhost2', 'vhost3');
		$this->assertSame($expected, $helper->getSubdirectories(vfsStream::url('var/www/')));
	}

}