<?php

namespace Derhansen\Quixr\Commands\Analyze;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Derhansen\Quixr\Util\Returncodes;
use Derhansen\Quixr\Commands\Command;

class TrafficCommand extends Command {

	/**
	 * Configuration
	 *
	 * @return void
	 */
	protected function configure() {
		$this
			->setName('analyze:traffic')
			->setDescription('Analyzes traffic for all virtual hosts in a given path')
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

		$targetFile = $input->getArgument('target-file');
		if (!$this->getQuixr()->getFilesystem()->checkTargetFileWriteable($targetFile)) {
			$output->writeln('Target file is not writeable');
			return Returncodes::TARGET_FILE_NOT_WRITEABLE;
		}

		// Sets the logformat
		$this->getQuixr()->getLoganalyzer()->setLogformat($input->getArgument('logformat'));

		// Get historical data from target JSON file
		// @todo - Catch exception for invalid JSON data
		$trafficData = $this->getQuixr()->getFilesystem()->getTargetJSONAsArray($targetFile);

		foreach ($dirs as $vhost) {
			$logfile = $input->getArgument('vhost-path') . $vhost . '/' .$input->getArgument('logfile-path') .
				'/' . $input->getArgument('logfile');

			$currentTraffic = array();
			if (file_exists($logfile)) {
				if (isset($trafficData[$vhost])) {
					$currentTraffic[$vhost] = $trafficData[$vhost];
				} else {
					$currentTraffic = $this->getQuixr()->getLoganalyzer()->getEmptyVhostData($vhost);
				}
				$newData = $this->getQuixr()->getLoganalyzer()->analyzeLogfile($logfile, $currentTraffic);
				$trafficData[$vhost] = $newData[$vhost];
			} else {
				// @todo print error about missing logfile
			}

			// @todo write new target file after each iteration
			// @todo Out into function in Derhansen\Quixr\Util\Filesystem
			$handle = fopen($targetFile, 'w');
			fwrite($handle, json_encode($trafficData)); // Use JSON_PRETTY_PRINT if PHP >= 5.4
			fclose($handle);
		}

		// Use below with: ./bin/quixr analyze:traffic /var/www/ logfiles access_log traffic.json common
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', $vhostData));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400, 10519961, 'dc0158f34f1135bfa6cefb72bcc7b4e4'));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400));
		//print_r($this->getQuixr()->getLoganalyzer()->analyzeLogfile('/var/www/test1.typo3.local/logfiles/access_log', 'vhost1', 1388534400));

		$output->writeln('Do something here ' . $this->getQuixr()->getLoganalyzer()->dummy());
	}

}