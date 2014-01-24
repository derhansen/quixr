<?php

namespace tests\unit\Derhansen\Quixr\Commands;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Derhansen\Quixr\Commands\AnalyzeCommand;
use Derhansen\Quixr\Helper\LogfileHelper;

require_once __DIR__ . '/../../../bootstrap.php';

class TestCommandTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test if command returns expected string
	 *
	 * @test
	 */
	public function testExecute() {
		$application = new Application();
		$application->getHelperSet()->set(new LogfileHelper());
		$application->add(new AnalyzeCommand());

		$command = $application->find('analyze');
		$commandTester = new CommandTester($command);
		$commandTester->execute(
			array(
				'command' => $command->getName(),
				'vhost-path' => '/var/www/',
				'logfile-path' => 'logfiles',
				'logfiles' => 'access.log',
				'target-file' => 'traffic.json'
			)
		);

		$this->assertRegExp('/^Do something here From Helper$/', $commandTester->getDisplay());
	}

}