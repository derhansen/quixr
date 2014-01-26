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
	 */
	function __construct() {
		$this->parser = new ApacheLogParser("%h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"");
	}


	/**
	 * @param $logfile
	 * @param string $vhostname
	 * @param int $starttime
	 * @param $offset
	 * @param string $comparehash
	 * @return array
	 */
	public function analyzeLogfile($logfile, $vhostname = '', $starttime = 0, $offset = -1, $comparehash = '') {
		// @todo Remove when option for format is implemented
		$this->parser->setFormat("%h %l %u %t \"%r\" %>s %O");

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
		if ($handle) {

			// Check $offset and $comparehash
			// Try to put pointer on offset and check if MD5 of line at offset matches given MD5
			// If not, rewind file handle
			// @todo - Make own method with return
			if ($offset > -1 && $comparehash != '') {
				if (fseek($handle, $offset, SEEK_CUR) === 0) {
					$compareline = fgets($handle, 4096);
					if (md5($compareline) !== $comparehash) {
						rewind($handle);
					}
				}
			}

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
		}

		//print_r($ret);
		return $ret;
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
