<?php

namespace Derhansen\Quixr\Helper;

use Symfony\Component\Console\Helper\Helper;

class LogfileHelper extends Helper {

	/**
	 * Returns the canonical name of this helper.
	 *
	 * @return string The canonical name
	 *
	 * @api
	 */
	public function getName() {
		return 'logfile';
	}

	/**
	 * Dummy method
	 *
	 * @return string
	 */
	public function dummy() {
		return 'From Helper';
	}

}