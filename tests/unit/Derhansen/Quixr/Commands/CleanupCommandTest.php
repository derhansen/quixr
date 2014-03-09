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
			),
			'empty' => array()
		);
		$this->root = vfsStream::setup('var', 777, $structure);

		$application = new Application();
		$application->add(new CleanupCommand());

		$this->command = $application->find('cleanup');
		$this->commandTester = new CommandTester($this->command);
	}

	/**
	 * Test if command returns expected returncode if vhost-path is not found
	 *
	 * @test
	 */
	public function vhostPathNotFoundTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/ww/'),
				'target-file' => 'quixr.json'
			)
		);
		$this->assertEquals(Returncodes::PATH_NOT_FOUND_OR_EMPTY, $this->commandTester->getStatusCode());
	}

	/**
	 * Test if command returns expected returncode if vhost-path is empty
	 *
	 * @test
	 */
	public function vhostPathEmptyTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/empty/'),
				'target-file' => 'quixr.json'
			)
		);
		$this->assertEquals(Returncodes::PATH_NOT_FOUND_OR_EMPTY, $this->commandTester->getStatusCode());
	}

	/**
	 * Test if command returns expected returncode if target file not writeable
	 *
	 * @test
	 */
	public function targetFileNotWriteableTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'target-file' => vfsStream::url('test.json')
			)
		);
		$this->assertEquals(Returncodes::TARGET_FILE_NOT_WRITEABLE, $this->commandTester->getStatusCode());
	}

	/**
	 * Test if the cleanup command executes successfully
	 *
	 * @test
	 */
	public function commandExecutesSuccessfullTest() {
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'target-file' => vfsStream::url('var/www/quixr.json')
			)
		);
		$this->assertEquals(Returncodes::SUCCESS, $this->commandTester->getStatusCode());
	}

	/**
	 * Test if the cleanup command removes unavailable vhost
	 *
	 * @test
	 */
	public function unavailableVhostGetsRemovedFromJsonFileTest() {
		$file = vfsStream::url('var/www/quixr.json');
		$exampleJson = array('vhost1' => array(), 'vhost2' => array(), 'vhost3' => array(), 'vhost4' => array());
		file_put_contents($file, json_encode($exampleJson));
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'target-file' => $file
			)
		);
		$actual = json_decode(file_get_contents($file), TRUE);
		$expected = array('vhost1' => array(), 'vhost2' => array(), 'vhost3' => array());
		$this->assertEquals($expected, $actual);
	}

}