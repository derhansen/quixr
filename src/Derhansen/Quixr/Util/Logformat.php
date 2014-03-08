<?php

namespace Derhansen\Quixr\Util;

/**
 * @author Torben Hansen <derhansen@gmail.com>
 */
class Logformat {

	/**
	 * Parser format for combined logfiles
	 */
	const COMBINED = '%h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"';

	/**
	 * Parser format for common logfiles
	 */
	const COMMON = '%h %l %u %t \"%r\" %>s %O';

} 