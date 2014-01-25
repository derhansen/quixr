<?php

namespace Derhansen\Quixr\Helper;

use Symfony\Component\Console\Helper\Helper;

class FilesystemHelper extends Helper {

	/**
	 * Returns the canonical name of this helper.
	 *
	 * @return string The canonical name
	 *
	 * @api
	 */
	public function getName() {
		return 'filesystem';
	}

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