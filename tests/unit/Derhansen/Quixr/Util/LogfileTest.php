<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use Derhansen\Quixr\Util\Logparser;

class LogfileTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Logparser
	 */
	protected $logfile;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		$this->logfile = new Logparser();
	}

	/**
	 * @test
	 */
	public function dummyTest() {
		$this->assertEquals('From Helper', $this->logfile->dummy());
	}

}