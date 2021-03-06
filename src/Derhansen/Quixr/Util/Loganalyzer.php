<?php

namespace Derhansen\Quixr\Util;

use Kassner\LogParser\LogParser;
use Derhansen\Quixr\Exceptions\AnalyzeLogfileException;

/**
 * @author Torben Hansen <derhansen@gmail.com>
 */
class Loganalyzer {

	/**
	 * @var LogParser
	 */
	protected $parser;

	/**
	 * Constructor - sets default logformat to combined
	 *
	 * @param mixed $parser
	 */
	function __construct($parser = NULL) {
		if (!$parser) {
			$this->parser = new LogParser();
		} else {
			$this->parser = $parser;
		}
		$this->parser->setFormat(Logformat::COMBINED);
	}

	/**
	 * Analyzes the logfile and updates the given array with vhost traffic data
	 *
	 * @param string $logfile The logfile
	 * @param array $vhostData Array with traffic data for vhost
	 * @throws AnalyzeLogfileException
	 * @return bool|array
	 */
	public function analyzeLogfile($logfile, $vhostData) {
		$vhostname = current(array_keys($vhostData));
		$starttime = $vhostData[$vhostname]['quixr']['traffic_lasttstamp'];
		$offset = $vhostData[$vhostname]['quixr']['traffic_lastoffset'];
		$comparehash = $vhostData[$vhostname]['quixr']['traffic_lastlinehash'];

		$lastOffset = 0;

		try {
			$handle = fopen($logfile, 'r');
			if (!$handle) {
				return FALSE;
			}
		} catch(\Exception $e) {
			throw new AnalyzeLogfileException($e->getMessage(), $e->getCode(), $e);
		}

		// Set handle to offset
		$handle = $this->setHandleToOffset($handle, $offset, $comparehash);

		while (!feof($handle)) {
			$rawline = fgets($handle, 8190);
			// Ignore blank lines
			if ($rawline == '') continue;
			try {
				$lineObj = $this->parser->parse($rawline);
				if ($lineObj->stamp > $starttime) {
					$previousOffset = $lastOffset;
					$lastOffset = ftell($handle);

					if (isset($vhostData[$vhostname]['traffic'][date('Y', $lineObj->stamp)][date('n', $lineObj->stamp)][date('j', $lineObj->stamp)])) {
						$vhostData[$vhostname]['traffic'][date('Y', $lineObj->stamp)][date('n', $lineObj->stamp)][date('j', $lineObj->stamp)] += $lineObj->sentBytes;
					} else {
						$vhostData[$vhostname]['traffic'][date('Y', $lineObj->stamp)][date('n', $lineObj->stamp)][date('j', $lineObj->stamp)] = $lineObj->sentBytes;
					}
					$vhostData[$vhostname]['quixr']['traffic_lasttstamp'] = $lineObj->stamp;
					$vhostData[$vhostname]['quixr']['traffic_lastoffset'] = $previousOffset;
					$vhostData[$vhostname]['quixr']['traffic_lastlinehash'] = md5($rawline);
				}
			} catch(\Exception $e) {
				// Ignore parsing errors for lines in logfile
			}
		}

		fclose($handle);
		return $vhostData;
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
	 * Returns an empty array with vhost data
	 *
	 * @param string $vhost
	 * @return array
	 */
	public function getEmptyVhostData($vhost) {
		$vhostData = array(
			$vhost => array(
				'traffic' => array(),
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
