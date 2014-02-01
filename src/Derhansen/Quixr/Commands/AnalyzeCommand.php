<?php

namespace Derhansen\Quixr\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Derhansen\Quixr\Util\Returncodes;

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
			->addArgument('logformat', InputArgument::OPTIONAL, 'Apache2 Logfile format. Allowed values: common, combined', 'combined')
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
		$dirs = $this->getQuixr()->getFilesystem()->getSubdirectories($input->getArgument('vhost-path'));
		if ($dirs === FALSE | count($dirs) === 0) {
			$output->writeln('Given vhost-path not found or empty');
			return Returncodes::PATH_NOT_FOUND_OR_EMPTY;
		}

		// Sets the logformat
		$this->getQuixr()->getLoganalyzer()->setLogformat($input->getArgument('logformat'));

		// Use below with: ./bin/quixr analyze /var/www/ logfiles access_log traffic.json common
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400, 10519961, 'dc0158f34f1135bfa6cefb72bcc7b4e4'));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400));
		// @todo - Make analyzeLogfile accept an array (from existing traffic data file)

		$output->writeln('Do something here ' . $this->getQuixr()->getLoganalyzer()->dummy());
	}

}