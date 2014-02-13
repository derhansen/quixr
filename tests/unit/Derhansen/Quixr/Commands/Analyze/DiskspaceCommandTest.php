<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

use Derhansen\Quixr\Console\Application;
use Derhansen\Quixr\Commands\Analyze\DiskspaceCommand;
use Derhansen\Quixr\Util\Returncodes;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/../../../../bootstrap.php';

class DiskspaceCommandTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Command
	 */
	protected $command;

	/**
	 * @var CommandTester::
	 */
	protected $commandTester;

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp() {
		$application = new Application();
		$application->add(new DiskspaceCommand());

		$this->command = $application->find('analyze:diskspace');
		$this->commandTester = new CommandTester($this->command);
	}

 	/**
	 * Test if command returns expected string
	 *
	 * @test
	 */
	public function commandExecutesSuccessfullTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'document-root' => 'htdocs',
				'target-file' => 'quixr.json'
			)
		);
		$this->assertRegExp('/^Not implemented yet$/', $this->commandTester->getDisplay());
	}

}