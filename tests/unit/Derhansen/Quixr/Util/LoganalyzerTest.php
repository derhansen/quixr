<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use Derhansen\Quixr\Util\Loganalyzer;
use org\bovigo\vfs\vfsStream;

class LoganalyzerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Loganalyzer
	 */
	protected $loganalyzer;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		vfsStream::setup('var');
		$this->loganalyzer = new Loganalyzer();
	}

	/**
	 * @test
	 */
	public function setHandleToOffsetWithMissingHandleTest() {
		$this->assertEquals(NULL, $this->loganalyzer->setHandleToOffset(NULL, NULL, NULL));


	}

	/**
	 * @test
	 */
	public function setHandleToOffsetWithoutOffsetAndComparehashTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1');
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals($handle, $this->loganalyzer->setHandleToOffset($handle, NULL, NULL));
		fclose($handle);
	}

	/**
	 * @test
	 */
	public function setHandleToOffsetWithOffsetAndComparehashTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3' . PHP_EOL . 'line4' . PHP_EOL);
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals(18, ftell($this->loganalyzer->setHandleToOffset($handle, 12, md5('line3' . PHP_EOL))));
		fclose($handle);
	}

	/**
	 * @test
	 */
	public function setHandleToOffsetWithOffsetAndWrongComparehashTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3' . PHP_EOL . 'line4' . PHP_EOL);
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals(0, ftell($this->loganalyzer->setHandleToOffset($handle, 12, md5('line2' . PHP_EOL))));
		fclose($handle);
	}

	/**
	 * @test
	 */
	public function setHandleToOffsetWithWrongOffsetTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3' . PHP_EOL . 'line4' . PHP_EOL);
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals(0, ftell($this->loganalyzer->setHandleToOffset($handle, 99, md5('line2' . PHP_EOL))));
		fclose($handle);
	}

	/**
	 * @test
	 */
	public function dummyTest() {
		$this->assertEquals('From Helper', $this->loganalyzer->dummy());
	}

}