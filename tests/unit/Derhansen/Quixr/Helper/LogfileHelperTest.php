<?php

namespace tests\unit\Derhansen\Quixr\Commands;

use \Derhansen\Quixr\Helper\LogfileHelper;

class LogfileHelperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function dummyTest() {
		$helper = new LogfileHelper();
		$this->assertEquals('From Helper', $helper->dummy());
	}
}