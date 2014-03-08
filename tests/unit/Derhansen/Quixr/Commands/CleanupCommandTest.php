<?php

namespace tests\unit\Derhansen\Quixr\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Derhansen\Quixr\Console\Application;
use Derhansen\Quixr\Commands\CleanupCommand;
use Derhansen\Quixr\Util\Returncodes;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/../../../bootstrap.php';

class CleanupCommandTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \org\bovigo\vfs\vfsStreamDirectory
	 */
	protected $root;

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
		$structure = array(
			'www' => array(
				'vhost1' => array(),
				'vhost2' => array(),
				'vhost3' => array(),
			)
		);
		$this->root = vfsStream::setup('var', 777, $structure);

		$application = new Application();
		$application->add(new CleanupCommand());

		$this->command = $application->find('cleanup');
		$this->commandTester = new CommandTester($this->command);
	}

	/**
	 * Test if the cleanup command executes successfully
	 *
	 * @test
	 */
	public function cleanupCommandTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'target-file' => vfsStream::url('var/www/quixr.json')
			)
		);
		$this->assertEquals(Returncodes::SUCCESS, $this->commandTester->getStatusCode());
	}
}