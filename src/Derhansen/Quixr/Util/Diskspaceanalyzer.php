<?php

namespace Derhansen\Quixr\Util;

class Diskspaceanalyzer {

	/**
	 * Analyzes the dirsize of the given document root and updates the given array with vhost dirsize data
	 * If data for today exists, they just get overwritten
	 *
	 * @param string $documentRoot Document root
	 * @param array $vhostData Array with historical data for vhost
	 * @return array
	 */
	public function analyzeDiskspace($documentRoot, $vhostData) {
		$vhostname = current(array_keys($vhostData));
		$vhostData[$vhostname]['diskspace'][date('Y')][date('n')][date('j')] = $this->getDirectorySize($documentRoot);
		return $vhostData;
	}

	/**
	 * Returns the directory size in bytes
	 *
	 * @param string $directory
	 * @return int
	 */
	public function getDirectorySize($directory) {
		$size = 0;
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory,
			\FilesystemIterator::SKIP_DOTS)) as $object){
			$size += $object->getSize();
		}
		return $size;
	}

}
