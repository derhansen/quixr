<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use \Derhansen\Quixr\Helper\LogfileHelper;

require_once __DIR__ . '/../../../bootstrap.php';

class LogfileHelperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function dummyTest() {
		$helper = new LogfileHelper();
		$this->assertEquals('From Helper', $helper->dummy());
	}

}