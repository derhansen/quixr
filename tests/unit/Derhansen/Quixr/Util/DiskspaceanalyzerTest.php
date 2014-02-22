<?php

namespace tests\unit\Derhansen\Quixr\Util;

use Derhansen\Quixr\Util\Diskspaceanalyzer;

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
	public function analyzeDiskspaceTest() {
		$this->assertTrue(TRUE);
	}
}