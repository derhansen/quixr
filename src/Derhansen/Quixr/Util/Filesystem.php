<?php

namespace Derhansen\Quixr\Util;

use Derhansen\Quixr\Exceptions\NoValidJSONException;

class Filesystem {

	/**
	 * Returns an array of subdirectories for the given path
	 *
	 * @param string $path The path
	 * @return array|bool
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

	/**
	 * Checks if the given file exists and is writeable.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function checkTargetFileWriteable($file) {
		$handle = @fopen($file, 'c+');
		if ($handle === FALSE) {
			return $handle;
		} else {
			fclose($handle);
			return TRUE;
		}
	}

	/**
	 * Reads the given file and returns the content as an array. If the file does not
	 * exist, an empty array is returned.
	 *
	 * @param string $file
	 * @throws NoValidJSONException
	 * @return array
	 */
	public function getTargetJSONAsArray($file) {
		if (!file_exists($file)) {
			return array();
		}
		$content = file_get_contents($file);
		if ($content === '') {
			return array();
		}
		if (!is_object(json_decode($content))) {
			throw new NoValidJSONException('File does not contain valid JSON data');
		}
		return json_decode($content, TRUE);
	}

	/**
	 * Saves the given data as JSON to file
	 *
	 * @todo Use JSON_PRETTY_PRINT if PHP >= 5.4
	 * @param string $file
	 * @param mixed $data
	 * @return void
	 */
	public function writeDataAsJSON($file, $data) {
		$handle = fopen($file, 'w');
		fwrite($handle, json_encode($data));
		fclose($handle);
	}
}
