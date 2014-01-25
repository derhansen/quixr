<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use Derhansen\Quixr\Util\Loganalyzer;

class LogfileTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Loganalyzer
	 */
	protected $logfile;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		$this->logfile = new Loganalyzer();
	}

	/**
	 * @test
	 */
	public function dummyTest() {
		$this->assertEquals('From Helper', $this->logfile->dummy());
	}

}