<?php

namespace Derhansen\Quixr\Util;

use Kassner\ApacheLogParser\ApacheLogParser;

class Loganalyzer {

	/**
	 * @var ApacheLogParser
	 */
	protected $parser;

	/**
	 * Constructor - sets default logformat to combined
	 *
	 * @param mixed $parser
	 */
	function __construct($parser = NULL) {
		if (!$parser) {
			$this->parser = new ApacheLogParser();
		} else {
			$this->parser = $parser;
		}
		$this->parser->setFormat(Logformat::COMBINED);
	}

	/**
	 * @param $logfile
	 * @param string $vhostname
	 * @param int $starttime
	 * @param $offset
	 * @param string $comparehash
	 * @return bool|array
	 */
	public function analyzeLogfile($logfile, $vhostname = '', $starttime = 0, $offset = -1, $comparehash = '') {
		$ret = array(
			$vhostname => array(
			'traffic' => array(),
			'lasttstamp' => 0,
			'lastoffset' => -1,
			'lastlinehash' => ''
			)
		);

		$lastOffset = 0;

		// @todo Replace die() with Exception
		$handle = fopen($logfile, 'r') or die('Couldnt get handle');
		if (!$handle) {
			return FALSE;
		}

		// Set handle to offset
		$handle = $this->setHandleToOffset($handle, $offset, $comparehash);

		while (!feof($handle)) {
			$rawline = fgets($handle, 4096);
			try {
				$lineObj = $this->parser->parse($rawline);
				if ($lineObj->stamp > $starttime) {
					$previousOffset = $lastOffset;
					$lastOffset = ftell($handle);

					if (isset($ret[$vhostname]['traffic'][date('Y', $lineObj->stamp)][date('m', $lineObj->stamp)][date('d', $lineObj->stamp)])) {
						$ret[$vhostname]['traffic'][date('Y', $lineObj->stamp)][date('m', $lineObj->stamp)][date('d', $lineObj->stamp)] += $lineObj->sentBytes;
					} else {
						$ret[$vhostname]['traffic'][date('Y', $lineObj->stamp)][date('m', $lineObj->stamp)][date('d', $lineObj->stamp)] = $lineObj->sentBytes;
					}
					$ret[$vhostname]['lasttstamp'] = $lineObj->stamp;
					$ret[$vhostname]['lastoffset'] = $previousOffset;
					$ret[$vhostname]['lastlinehash'] = md5($rawline);
				}
			} catch(\Exception $e) {
				// @todo handle exception
			}
		}
		fclose($handle);

		return $ret;
	}

	/**
	 * Sets the given handle to the given offset if exists. Also compares md5 hash for found previous line
	 * at position with given comparehash
	 *
	 * @param resource $handle
	 * @param int $offset
	 * @param string $comparehash
	 * @return resource
	 */
	public function setHandleToOffset($handle, $offset, $comparehash) {
		if ($offset > -1 && $comparehash != '') {
			if (fseek($handle, $offset, SEEK_CUR) === 0) {
				$compareline = fgets($handle, 4096);
				if (md5($compareline) !== $comparehash) {
					rewind($handle);
				}
			}
		}
		return $handle;
	}

	/**
	 * Sets the format of the logfile
	 *
	 * @param string $format
	 */
	public function setLogformat($format) {
		switch ($format) {
			case 'common':
				$this->parser->setFormat(Logformat::COMMON);
				break;
			case 'combined':
				$this->parser->setFormat(Logformat::COMBINED);
				break;
			default:
				$this->parser->setFormat(Logformat::COMBINED);
		}
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
