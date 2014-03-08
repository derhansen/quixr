<?php

namespace Derhansen\Quixr\Commands;

use Derhansen\Quixr\Quixr;
use Derhansen\Quixr\Console\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * @author Torben Hansen <derhansen@gmail.com>
 */
abstract class Command extends BaseCommand {

	/**
	 * @var Quixr
	 */
	private $quixr;

	/**
	 * @return Quixr
	 */
	public function getQuixr() {
		/** @var $application Application */
		$application = $this->getApplication();
		$this->quixr = $application->getQuixr();
		return $this->quixr;
	}

}