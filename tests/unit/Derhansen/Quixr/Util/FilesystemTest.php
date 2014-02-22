<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use Derhansen\Quixr\Util\Filesystem;
use org\bovigo\vfs\vfsStream;

class FilesystemTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Filesystem
	 */
	protected $filesysten;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		$this->filesysten = new Filesystem();
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfNoPathGivenTest() {
		$this->assertFalse($this->filesysten->getSubdirectories(NULL));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfEmptyPathGivenTest() {
		$this->assertFalse($this->filesysten->getSubdirectories(''));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfPathDoesNotEndWithSlashTest() {
		$this->assertFalse($this->filesysten->getSubdirectories('/var/www'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsFalseIfNonExistingPathGivenTest() {
		vfsStream::setup('var');
		$this->assertFalse($this->filesysten->getSubdirectories(vfsStream::url('var/www/')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getSubdirectoriesReturnsArrayOfDirsForExistingPathTest() {
		$root = vfsStream::setup('var');
		$wwwDir = vfsStream::newDirectory('www', 777);
		$wwwDir->addChild(vfsStream::newDirectory('vhost1', 777));
		$wwwDir->addChild(vfsStream::newDirectory('vhost2', 777));
		$wwwDir->addChild(vfsStream::newDirectory('vhost3', 777));
		$wwwDir->addChild(vfsStream::newFile('testfile.txt', 777));
		$root->addChild($wwwDir);

		$expected = array('vhost1', 'vhost2', 'vhost3');
		$this->assertSame($expected, $this->filesysten->getSubdirectories(vfsStream::url('var/www/')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getTargetJSONAsArrayFileReturnsEmptyArrayIfFileDoesNotExistTest() {
		$this->assertEquals(array(), $this->filesysten->getTargetJSONAsArray('/home/result.json'));
	}

	/**
	 * @test
	 * @expectedException \Derhansen\Quixr\Exceptions\NoValidJSONException
	 * @return void
	 */
	public function getTargetJSONAsArrayFileThrowsExceptionIfFileDoesNotContainJSONData() {
		vfsStream::setup('var');
		$file = vfsStream::url('var/target.json');
		file_put_contents($file, 'No valid JSON data');
		$this->filesysten->getTargetJSONAsArray(vfsStream::url('var/target.json'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getTargetJSONAsArrayFileReturnsArrayForValidJSONData() {
		vfsStream::setup('var');
		$file = vfsStream::url('var/target.json');
		$data = array('key' => 'value');
		file_put_contents($file, json_encode($data));
		$this->assertSame($data , $this->filesysten->getTargetJSONAsArray(vfsStream::url('var/target.json')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function checkTargetFileWriteableReturnsFalseIfFileExistsButNotWriteableTest() {
		$root = vfsStream::setup('var');
		vfsStream::newFile('test.txt', 000)->at($root)->setContent('');
		$this->assertFalse($this->filesysten->checkTargetFileWriteable(vfsStream::url('var/test.txt')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function checkTargetFileWriteableReturnsFalseIfFileNotExistsAndDirNotWriteableTest() {
		$root = vfsStream::setup('var');
		$subdir = vfsStream::newDirectory('subdir', 000);
		$root->addChild($subdir);
		$this->assertFalse($this->filesysten->checkTargetFileWriteable(vfsStream::url('var/subdir/test.json')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function checkTargetFileWriteableReturnsTrueIfFileNotExistsAndDirWriteableTest() {
		vfsStream::setup('var');
		$this->assertTrue($this->filesysten->checkTargetFileWriteable(vfsStream::url('var/test.json')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function checkTargetFileWriteableReturnsTrueIfFileExistsAndDirWriteableTest() {
		$root = vfsStream::setup('var');
		vfsStream::newFile('test.txt')->at($root)->setContent('');
		$this->assertTrue($this->filesysten->checkTargetFileWriteable(vfsStream::url('var/test.txt')));

	}

	/**
	 * @test
	 * @return void
	 */
	public function getTargetJSONAsArrayFileReturnsExpectedArrayIfFileExists() {
		$file = vfsStream::url('var/target.json');
		$jsonArray = array('key' => 'value');
		file_put_contents($file, json_encode($jsonArray));
		$this->assertEquals($jsonArray, $this->filesysten->getTargetJSONAsArray(vfsStream::url('var/target.json')));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getTargetJSONAsArrayFileReturnsExpectedArrayIfFileExistsButEmpty() {
		$file = vfsStream::url('var/target.json');
		file_put_contents($file, '');
		$this->assertEquals(array(), $this->filesysten->getTargetJSONAsArray(vfsStream::url('var/target.json')));
	}

	/**
	 * @test
	 * @expectedException \Derhansen\Quixr\Exceptions\NoValidJSONException
	 * @return void
	 */
	public function getTargetJSONAsArrayFileReturnsExceptionIfFileDoesNotContainValidJson() {
		$file = vfsStream::url('var/target.json');
		file_put_contents($file, 'Invalid JSON');
		$this->filesysten->getTargetJSONAsArray(vfsStream::url('var/target.json'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function writeDataAsJSONSavesToFileTest() {
		$root = vfsStream::setup('var');
		vfsStream::newFile('test.json')->at($root)->setContent('');
		$data = array('test1', 'test2', 'test3');
		$this->filesysten->writeDataAsJSON(vfsStream::url('var/test.json'), $data);
		$expected = '["test1","test2","test3"]';
		$actual = file_get_contents(vfsStream::url('var/test.json'));
		$this->assertSame($expected, $actual);
	}
}