<?php

namespace tests\unit\Derhansen\Quixr\Util;

use Derhansen\Quixr\Util\VhostData;

class VhostDataTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var VhostData::
	 */
	protected $vhostdata;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		$this->vhostdata = new VhostData();
	}

	/**
	 * @test
	 */
	public function getEmptyVhostDataReturnsExpectedArrayTest() {
		$expected = array(
			'vhost1' => array(
				'traffic' => array(),
				'diskspace' => array(),
				'quixr' => array(
					'traffic_lasttstamp' => 0,
					'traffic_lastoffset' => -1,
					'traffic_lastlinehash' => '',
					'diskspace_lasttstamp' => 0
				),
			)
		);
		$this->assertSame($expected, $this->vhostdata->getEmptyVhostData('vhost1'));
	}
}