<?php

namespace tests\unit\Derhansen\Quixr\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

use Derhansen\Quixr\Console\Application;
use Derhansen\Quixr\Commands\AnalyzeCommand;
use Derhansen\Quixr\Util\Returncodes;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/../../../bootstrap.php';

class TestCommandTest extends \PHPUnit_Framework_TestCase {

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
			),
			'empty' => array()
		);
		$this->root = vfsStream::setup('var', 777, $structure);

		$application = new Application();
		$application->add(new AnalyzeCommand());

		$this->command = $application->find('analyze');
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
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => 'traffic.json'
			)
		);
		$this->assertRegExp('/^Do something here From Helper$/', $this->commandTester->getDisplay());
	}

	/**
	 * Test if command returns expected returncode if vhost-path is not found
	 *
	 * @test
	 */
	public function vhostpPathNotFoundTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/ww/'),
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => 'traffic.json'
			)
		);
		$this->assertEquals(Returncodes::PATH_NOT_FOUND_OR_EMPTY, $this->commandTester->getStatusCode());
	}

	/**
	 * Test if command returns expected returncode if vhost-path is empty
	 *
	 * @test
	 */
	public function vhostpPathEmptyTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/empty/'),
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => 'traffic.json'
			)
		);
		$this->assertEquals(Returncodes::PATH_NOT_FOUND_OR_EMPTY, $this->commandTester->getStatusCode());
	}

}