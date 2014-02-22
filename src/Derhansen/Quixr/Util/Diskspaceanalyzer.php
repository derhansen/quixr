<?php

namespace Derhansen\Quixr\Util;

class Diskspaceanalyzer {

	/**
	 * Analyzes the dirsize of the given document root and updates the given array with vhost dirsize data
	 *
	 * @param string $documentRoot Document root
	 * @param array $vhostData Array with historical data for vhost
	 * @return array
	 */
	public function analyzeDiskspace($documentRoot, $vhostData) {
		return $vhostData;
	}

}
