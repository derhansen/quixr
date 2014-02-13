<?php

namespace Derhansen\Quixr\Commands\Analyze;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Derhansen\Quixr\Commands\Command;

class DiskspaceCommand extends Command {

	/**
	 * Configuration
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('analyze:diskspace')
			->setDescription('Analyzes diskspace for all virtual hosts in a given path')
			->addArgument('vhost-path', InputArgument::REQUIRED, 'Path to virtial hosts (e.g. /var/www/)')
			->addArgument('document-root', InputArgument::REQUIRED, 'Path to document root of each virtual host (e.g. htdocs)')
			->addArgument('target-file', InputArgument::REQUIRED, 'Target JSON file for analysis results (e.g. quixr.json')
		;
	}

	/**
	 * Executes the analyze:diskspace command
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return bool|int|null
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Not implemented yet');
	}

}