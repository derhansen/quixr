<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use Derhansen\Quixr\Util\Logfile;

class LogfileTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Logfile
	 */
	protected $logfile;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		$this->logfile = new Logfile();
	}

	/**
	 * @test
	 */
	public function dummyTest() {
		$this->assertEquals('From Helper', $this->logfile->dummy());
	}

}