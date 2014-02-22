<?php

namespace Derhansen\Quixr\Commands\Analyze;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Derhansen\Quixr\Util\Returncodes;
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

		// Get historical data from target JSON file
		// @todo - Catch exception for invalid JSON data
		$analysisData = $this->getQuixr()->getFilesystem()->getTargetJSONAsArray($targetFile);

		foreach ($dirs as $vhost) {
			$currentDiskspace = array();
			$documentRoot = $input->getArgument('vhost-path') . $vhost;
			if (is_dir($documentRoot)) {
				if (isset($analysisData[$vhost])) {
					$currentDiskspace[$vhost] = $analysisData[$vhost];
				} else {
					// @todo Use new shared function
					$currentDiskspace = array($vhost => array());
				}
				$newData = $this->getQuixr()->getDiskspaceanalyzer()->analyzeDiskspace($documentRoot, $currentDiskspace);
				$analysisData[$vhost] = $newData[$vhost];
			} else {
				// @todo print error about missing directory
			}

			$this->getQuixr()->getFilesystem()->writeDataAsJSON($targetFile, $analysisData);
		}

		$output->writeln('Not implemented yet');
		return Returncodes::SUCCESS;
	}

}