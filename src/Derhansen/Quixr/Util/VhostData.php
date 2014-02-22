<?php

namespace Derhansen\Quixr\Util;

class VhostData {

	/**
	 * Returns an empty array with vhost data
	 *
	 * @param string $vhost
	 * @return array
	 */
	public function getEmptyVhostData($vhost) {
		$vhostData = array(
			$vhost => array(
				'traffic' => array(),
				'diskspace' => array(),
				'quixr' => array(
					'traffic_lasttstamp' => 0,
					'traffic_lastoffset' => -1,
					'traffic_lastlinehash' => ''
				),
			)
		);
		return $vhostData;
	}
}
