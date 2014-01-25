<?php

namespace Derhansen\Quixr\Util;

class Filesystem {

	/**
	 * Returns an array of subdirectories for the given path
	 *
	 * @param string $path The path
	 * @return string|bool
	 */
	public function getSubdirectories($path) {
		if (is_dir($path) && substr($path, -1) == '/') {
			$results = scandir($path);
			$found = array();

			foreach ($results as $result) {
				if ($result === '.' or $result === '..') continue;

				if (is_dir($path . '/' . $result)) {
					$found[] = $result;
				}
			}

			return $found;
		} else {
			return FALSE;
		}
	}

}
