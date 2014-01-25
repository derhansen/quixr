<?php

namespace Derhansen\Quixr\Commands;

use Symfony\Component\Console\Input\InputArgument;
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
			->addArgument('vhost-path', InputArgument::REQUIRED, 'Path to virtial hosts (e.g. /var/www/)')
			->addArgument('logfile-path', InputArgument::REQUIRED, 'Path to logfiles in each virtual host (e.g. logs)')
			->addArgument('logfiles', InputArgument::REQUIRED, 'Logfiles (e.g. access.log, access.log.1')
			->addArgument('target-file', InputArgument::REQUIRED, 'Target JSON file for traffic analysis (e.g. traffic.json')
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
		/** @var \Derhansen\Quixr\Helper\LogfileHelper $logHelper */
		$logHelper = $this->getHelper('logfile');

		/** @var \Derhansen\Quixr\Helper\FilesystemHelper $fsHelper */
		$fsHelper = $this->getHelper('filesystem');

		//print_r($fsHelper->getSubdirectories('/var/www/'));

		$output->writeln('Do something here ' . $logHelper->dummy());
	}

}