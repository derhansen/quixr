<?php

namespace tests\unit\Derhansen\Quixr\Util;

use Derhansen\Quixr\Util\Diskspaceanalyzer;
use org\bovigo\vfs\vfsStream;

class DiskspaceanalyzerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Diskspaceanalyzer
	 */
	protected $diskspaceanalyzer;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		$this->diskspaceanalyzer = new Diskspaceanalyzer();
	}

	/**
	 * Dummy test
	 *
	 * @test
	 */
	public function analyzeDiskspaceReturnsExpectedArray() {
		$expected = array(
			'vhost1' => array(
				'diskspace' => array(
					date('Y') => array(
						date('n') => array(
							date('j') => 3072
						)
					)
				)
			)
		);
		$vhostData = array('vhost1' => array());
		$result = $this->diskspaceanalyzer->analyzeDiskspace(__DIR__ . '/../Fixtures/Diskspace', $vhostData);
		$this->assertEquals($expected, $result);
	}

	/**
	 * Dummy test
	 *
	 * @test
	 */
	public function analyzeDiskspaceReturnsOverwritesCurrentAndLeavesHistoricalData() {
		$vhostData = array(
			'vhost1' => array(
				'diskspace' => array(
					'1' => 2048,
					date('Y') => array(
						date('n') => array(
							date('j') => 1024
						)
					)
				)
			)
		);
		$expected = array(
			'vhost1' => array(
				'diskspace' => array(
					'1' => 2048,
					date('Y') => array(
						date('n') => array(
							date('j') => 3072
						)
					)
				)
			)
		);
		$result = $this->diskspaceanalyzer->analyzeDiskspace(__DIR__ . '/../Fixtures/Diskspace', $vhostData);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 */
	public function getDirectorySizeReturnsCorrectDirectorySize() {
		$this->assertEquals(3072, $this->diskspaceanalyzer->getDirectorySize(__DIR__ . '/../Fixtures/Diskspace'));
	}

	/**
	 * @test
	 * @expectedException \UnexpectedValueException
	 */
	public function getDirectorySizeThrowsExceptionIfPermissionDenied() {
		$root = vfsStream::setup('var');
		$wwwDir = vfsStream::newDirectory('www', 000);
		$root->addChild($wwwDir);
		$this->diskspaceanalyzer->getDirectorySize(vfsStream::url('var/www/'));
	}

}