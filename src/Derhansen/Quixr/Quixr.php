<?php

namespace Derhansen\Quixr;

use Derhansen\Quixr\Util\Logparser;
use Derhansen\Quixr\Util\Filesystem;

class Quixr {

	/**
	 * @var Logparser;
	 */
	private $logparser;

	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->logparser = new Logparser();
		$this->filesystem = new Filesystem();
	}

	/**
	 * @return \Derhansen\Quixr\Util\Logparser
	 */
	public function getLogparser() {
		return $this->logparser;
	}

	/**
	 * @return \Derhansen\Quixr\Util\Filesystem
	 */
	public function getFilesystem() {
		return $this->filesystem;
	}

}