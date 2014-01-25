<?php

namespace Derhansen\Quixr\Commands;

use Derhansen\Quixr\Quixr;
use Derhansen\Quixr\Console\Application;

use Symfony\Component\Console\Command\Command as BaseCommand;

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
		return $application->getQuixr();
	}

}