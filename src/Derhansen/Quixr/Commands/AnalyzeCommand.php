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
			->addArgument('logfile', InputArgument::REQUIRED, 'Logfile (e.g. access.log)')
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

		// @todo Check if target json file exists and is writeable
		$targetFile = $input->getArgument('target-file');

		// Sets the logformat
		$this->getQuixr()->getLoganalyzer()->setLogformat($input->getArgument('logformat'));

		// Get historical data from target JSON file
		$trafficData = $this->getQuixr()->getFilesystem()->getTargetJSONAsArray($targetFile);

		foreach ($dirs as $vhost) {
			$logfile = $input->getArgument('vhost-path') . $vhost . '/' .$input->getArgument('logfile-path') .
				'/' . $input->getArgument('logfile');

			if (file_exists($logfile)) {
				if (isset($trafficData[$vhost])) {
					$currentTraffic = $trafficData[$vhost];
				} else {
					$currentTraffic = $this->getQuixr()->getLoganalyzer()->getEmptyVhostData($vhost);
				}
				$trafficData[$vhost] = $this->getQuixr()->getLoganalyzer()->analyzeLogfile($logfile, $currentTraffic);
			} else {
				// @todo print error about missing logfile
			}

			// @todo write new target file after each iteration
			// @todo Out into function in Derhansen\Quixr\Util\Filesystem
			$handle = fopen($targetFile, 'w');
			fwrite($handle, json_encode($trafficData)); // Use JSON_PRETTY_PRINT if PHP >= 5.4
			fclose($handle);
		}

		// Use below with: ./bin/quixr analyze /var/www/ logfiles access_log traffic.json common
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', $vhostData));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400, 10519961, 'dc0158f34f1135bfa6cefb72bcc7b4e4'));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400));

		$output->writeln('Do something here ' . $this->getQuixr()->getLoganalyzer()->dummy());
	}

}