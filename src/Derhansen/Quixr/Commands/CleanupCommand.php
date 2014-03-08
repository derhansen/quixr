<?php

namespace Derhansen\Quixr\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Derhansen\Quixr\Util\Returncodes;

/**
 * @author Torben Hansen <derhansen@gmail.com>
 */
class CleanupCommand extends Command {

	/**
	 * Configuration
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('cleanup')
			->setDescription('Removes deleted vhosts from the given  JSON file with analysis results')
			->addArgument('target-file', InputArgument::REQUIRED, 'Target JSON file with analysis results (e.g. quixr.json')
		;
	}

	/**
	 * Executes the cleanup command
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return boolean|null
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		return Returncodes::SUCCESS;
	}
}