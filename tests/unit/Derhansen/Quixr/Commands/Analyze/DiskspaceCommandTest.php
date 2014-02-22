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
				'vhost1' => array(
					'htdocs' => array()
				),
				'vhost2' => array(
					'htdocs' => array()
				),
				'vhost3' => array(
					'htdocs' => array()
				),
			),
			'empty' => array()
		);
		$this->root = vfsStream::setup('var', 777, $structure);

		$application = new Application();
		$application->add(new DiskspaceCommand());

		$this->command = $application->find('analyze:diskspace');
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
				'document-root' => 'htdocs',
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
				'document-root' => 'htdocs',
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
				'document-root' => 'htdocs',
				'target-file' => vfsStream::url('quixr.json')
			)
		);
		$this->assertEquals(Returncodes::TARGET_FILE_NOT_WRITEABLE, $this->commandTester->getStatusCode());
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
				'target-file' => vfsStream::url('var/www/quixr.json')
			)
		);
		$this->assertRegExp('/^Not implemented yet$/', $this->commandTester->getDisplay());
	}

}