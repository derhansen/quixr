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
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->logfile = new Logfile();
		$this->logfile = new Filesystem();
	}

	/**
	 * @return \Derhansen\Quixr\Util\Logfile
	 */
	public function getLogfile() {
		return $this->logfile;
	}

	/**
	 * @return \Derhansen\Quixr\Util\Filesystem
	 */
	public function getFilesystem() {
		return $this->filesystem;
	}

}