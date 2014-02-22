<?php

namespace Derhansen\Quixr\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Derhansen\Quixr\Quixr;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication {

	/**
	 * @var Quixr
	 */
	private $quixr;

	/**
	 * Runs the application
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	public function doRun(InputInterface $input, OutputInterface $output) {
		$this->quixr = $this->getQuixr();
		return parent::doRun($input, $output);
	}

	/**
	 * Returns a new quixr instance
	 *
	 * @return Quixr
	 */
	public function getQuixr() {
		if ($this->quixr === null) {
			$this->quixr = new Quixr();
		}
		return $this->quixr;
	}
}
