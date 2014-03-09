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
			->addArgument('vhost-path', InputArgument::REQUIRED, 'Path to virtial hosts (e.g. /var/www/)')
			->addArgument('target-file', InputArgument::REQUIRED, 'Target JSON file with analysis results (e.g. quixr.json)')
		;
	}

	/**
	 * Executes the cleanup command and removes vhosts from the given JSON file, if they are not found in the given
	 * vhost path
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return boolean|null
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
		$unavailableVhosts = $analysisData;

		// Check available vhosts
		foreach ($dirs as $vhost) {
			if (isset($analysisData[$vhost])) {
				unset($unavailableVhosts[$vhost]);
			}
		}

		if (count($unavailableVhosts) > 0) {
			foreach ($unavailableVhosts as $unavailableVhost => $unavailableVhostData) { // @todo Hier den Index nehmen
				$output->writeln('<comment>Vhost not found: ' . $input->getArgument('vhost-path') . $unavailableVhost .
					' - removed from JSON file.</comment>');
				unset($analysisData[$unavailableVhost]);
			}
			// Save cleaned up JSON file
			$this->getQuixr()->getFilesystem()->writeDataAsJSON($targetFile, $analysisData);
		} else {
			$output->writeln('<info>Nothing to clean up.</info>');
		}

		return Returncodes::SUCCESS;
	}
}