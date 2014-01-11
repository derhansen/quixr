<?php

namespace Derhansen\Quixr\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCommand extends Command {

	/**
	 * Configuration
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('analyze')
			->setDescription('Add Description here')
		;
	}

	/**
	 * Executes the analyze command
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return bool|int|null
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Do something here');
	}
}