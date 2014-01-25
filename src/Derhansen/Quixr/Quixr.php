<?php

namespace Derhansen\Quixr;

use Derhansen\Quixr\Util\Loganalyzer;
use Derhansen\Quixr\Util\Filesystem;

class Quixr {

	/**
	 * @var Loganalyzer;
	 */
	private $loganalyzer;

	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->loganalyzer = new Loganalyzer();
		$this->filesystem = new Filesystem();
	}

	/**
	 * @return \Derhansen\Quixr\Util\Loganalyzer
	 */
	public function getLoganalyzer() {
		return $this->loganalyzer;
	}

	/**
	 * @return \Derhansen\Quixr\Util\Filesystem
	 */
	public function getFilesystem() {
		return $this->filesystem;
	}

}