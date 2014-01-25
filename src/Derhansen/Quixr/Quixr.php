<?php

namespace Derhansen\Quixr;

use Derhansen\Quixr\Util\Logfile;
use Derhansen\Quixr\Util\Filesystem;

class Quixr {

	/**
	 * @var Logfile;
	 */
	private $logfile;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->logfile = new Logfile();
	}

	/**
	 * @return \Derhansen\Quixr\Util\Logfile
	 */
	public function getLogfile() {
		return $this->logfile;
	}

}