<?php

namespace tests\Derhansen\Cli\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Derhansen\Quixr\Commands\AnalyzeCommand;

require_once __DIR__ . '/../../../bootstrap.php';

class TestCommandTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test if command returns expected string
	 *
	 * @test
	 */
	public function testExecute() {
		$application = new Application();
		$application->add(new AnalyzeCommand());

		$command = $application->find('analyze');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array('command' => $command->getName()));

		$this->assertRegExp('/Do something here/', $commandTester->getDisplay());
	}

}