<?php

namespace Derhansen\Quixr;

use Derhansen\Quixr\Util\Loganalyzer;
use Derhansen\Quixr\Util\Diskspaceanalyzer;
use Derhansen\Quixr\Util\Filesystem;
use Derhansen\Quixr\Util\VhostData;

/**
 * @author Torben Hansen <derhansen@gmail.com>
 */
class Quixr {

	/**
	 * @var Loganalyzer;
	 */
	private $loganalyzer;

	/**
	 * @var Diskspaceanalyzer
	 */
	private $diskspaceanalyzer;

	/**
	 * @var VhostData::
	 */
	private $vhostdata;

	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->loganalyzer = new Loganalyzer();
		$this->diskspaceanalyzer = new Diskspaceanalyzer();
		$this->vhostdata = new VhostData();
		$this->filesystem = new Filesystem();
	}

	/**
	 * @return \Derhansen\Quixr\Util\Loganalyzer
	 */
	public function getLoganalyzer() {
		return $this->loganalyzer;
	}

	/**
	 * @return \Derhansen\Quixr\Util\Diskspaceanalyzer
	 */
	public function getDiskspaceanalyzer() {
		return $this->diskspaceanalyzer;
	}

	/**
	 * @return \Derhansen\Quixr\Util\VhostData
	 */
	public function getVhostdata() {
		return $this->vhostdata;
	}

	/**
	 * @return \Derhansen\Quixr\Util\Filesystem
	 */
	public function getFilesystem() {
		return $this->filesystem;
	}

}
