<?php

namespace tests\unit\Derhansen\Quixr\Commands\Analyze;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

use Derhansen\Quixr\Console\Application;
use Derhansen\Quixr\Commands\Analyze\TrafficCommand;
use Derhansen\Quixr\Util\Returncodes;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/../../../../bootstrap.php';

class TrafficCommandTest extends \PHPUnit_Framework_TestCase {

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
					'logfiles' => array()
				),
				'vhost2' => array(
					'logfiles' => array()
				),
				'vhost3' => array(
					'logfiles' => array()
				),
			),
			'empty' => array()
		);
		$this->root = vfsStream::setup('var', 777, $structure);

		$application = new Application();
		$application->add(new TrafficCommand());

		$this->command = $application->find('analyze:traffic');
		$this->commandTester = new CommandTester($this->command);
	}

	/**
	 * Copies logfiles with initial data from fixtures to virtual directory structure
	 *
	 * @param string $format The format: common or combined
	 * @return void
	 */
	private function copyInitialLogfiles($format) {
		for ($i=1; $i <= 3; $i++) {
			copy (__DIR__ . '/../../Fixtures/' . $format . '.log',
				vfsStream::url('var/www/vhost' .  $i . '/logfiles/access.log'));
		}
	}

	/**
	 * Copies logfiles with new data from fixtures to virtual directory structure
	 *
	 * @param string $format The format: common or combined
	 * @return void
	 */
	private function copyNewLogfiles($format) {
		for ($i=1; $i <= 3; $i++) {
			copy (__DIR__ . '/../../Fixtures/new_' . $format . '.log',
				vfsStream::url('var/www/vhost' .  $i . '/logfiles/access.log'));
		}
	}

	/**
	 * Copies initial JSON Data to the target file for the given format
	 *
	 * @param string $file
	 * @param string $format
	 * @return void
	 */
	private function copyInitialJSONDataToTargetFile($file, $format) {
		copy (__DIR__ . '/../../Fixtures/result_init_' . $format . '.json', $file);
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
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => vfsStream::url('test.json')
			)
		);
		$this->assertEquals(Returncodes::TARGET_FILE_NOT_WRITEABLE, $this->commandTester->getStatusCode());
	}

	/**
	 * Test if command executes successfull
	 *
	 * @test
	 */
	public function commandExecutesSuccessfullTest() {
		$this->copyInitialLogfiles('combined');
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => vfsStream::url('var/www/quixr.json')
			)
		);
		$this->assertEquals(Returncodes::SUCCESS, $this->commandTester->getStatusCode());
	}

	/**
	 * Test if resulting JSON file contains expected data for combined logfiles
	 *
	 * @test
	 */
	public function initialTrafficDataGetsWrittenForCombinedLogfilesTest() {
		$this->copyInitialLogfiles('combined');
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => vfsStream::url('var/www/quixr.json')
			)
		);
		$expected = file_get_contents(__DIR__ . '/../../Fixtures/result_init_combined.json');
		$actual = file_get_contents(vfsStream::url('var/www/quixr.json'));
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test if resulting JSON file contains expected data for common logfiles
	 *
	 * @test
	 */
	public function initialTrafficDataGetsWrittenForCommonLogfilesTest() {
		$this->copyInitialLogfiles('common');
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => vfsStream::url('var/www/quixr.json'),
				'logformat' => 'common'
			)
		);
		$expected = file_get_contents(__DIR__ . '/../../Fixtures/result_init_common.json');
		$actual = file_get_contents(vfsStream::url('var/www/quixr.json'));
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test if new traffic data gets merged for combined logfiles
	 *
	 * @test
	 */
	public function newTrafficDataGetsMergedForCombinedLogfilesTest() {
		$this->copyNewLogfiles('combined');
		$this->copyInitialJSONDataToTargetFile(vfsStream::url('var/www/quixr.json'), 'combined');
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => vfsStream::url('var/www/quixr.json')
			)
		);
		$expected = file_get_contents(__DIR__ . '/../../Fixtures/result_new_combined.json');
		$actual = file_get_contents(vfsStream::url('var/www/quixr.json'));
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test if new traffic data gets merged for common logfiles
	 *
	 * @test
	 */
	public function newTrafficDataGetsMergedForCommonLogfilesTest() {
		$this->copyNewLogfiles('common');
		$this->copyInitialJSONDataToTargetFile(vfsStream::url('var/www/quixr.json'), 'common');
		$this->commandTester->execute(
			array(
				'command' => $this->command->getName(),
				'vhost-path' => vfsStream::url('var/www/'),
				'logfile-path' => 'logfiles',
				'logfile' => 'access.log',
				'target-file' => vfsStream::url('var/www/quixr.json'),
				'logformat' => 'common'
			)
		);
		$expected = file_get_contents(__DIR__ . '/../../Fixtures/result_new_common.json');
		$actual = file_get_contents(vfsStream::url('var/www/quixr.json'));
		$this->assertSame($expected, $actual);
	}

}