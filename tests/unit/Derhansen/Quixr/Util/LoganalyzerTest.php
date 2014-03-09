<?php

namespace tests\unit\Derhansen\Quixr\Helper;

use Derhansen\Quixr\Util\Loganalyzer;
use Derhansen\Quixr\Util\Logformat;
use org\bovigo\vfs\vfsStream;

class LoganalyzerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Loganalyzer
	 */
	protected $loganalyzer;

	/**
	 * Setup
	 * @return void
	 */
	public function setUp() {
		vfsStream::setup('var');
		$this->loganalyzer = new Loganalyzer();
	}

	/**
	 * @test
	 * @return void
	 */
	public function loganalyzerSetsCombinedAsDefaultLogformatInConstructorTest() {
		$mockLoganalyzer = $this->getMock('Kassner\LogParser\LogParser');
		$mockLoganalyzer->expects($this->once())->method('setFormat')->with(Logformat::COMBINED);
		new Loganalyzer($mockLoganalyzer);
	}

	/**
	 * @test
	 * @return void
	 */
	public function setLogformatSetsGivenLogformatTest() {
		$mockLoganalyzer = $this->getMock('Kassner\LogParser\LogParser');
		$mockLoganalyzer->expects($this->at(0))->method('setFormat')->with(Logformat::COMBINED);
		$mockLoganalyzer->expects($this->at(1))->method('setFormat')->with(Logformat::COMMON);
		$localLoganalyzer = new Loganalyzer($mockLoganalyzer);
		$localLoganalyzer->setLogformat('common');
	}

	/**
	 * @test
	 * @return void
	 */
	public function setLogformatSetsDefaultFormatIfWrongFormatGivenTest() {
		$mockLoganalyzer = $this->getMock('Kassner\LogParser\LogParser');
		$mockLoganalyzer->expects($this->at(0))->method('setFormat')->with(Logformat::COMBINED);
		$mockLoganalyzer->expects($this->at(1))->method('setFormat')->with(Logformat::COMBINED);
		$localLoganalyzer = new Loganalyzer($mockLoganalyzer);
		$localLoganalyzer->setLogformat('non-existing');
	}

	/**
	 * @test
	 * @return void
	 */
	public function setHandleToOffsetWithMissingHandleTest() {
		$this->assertEquals(NULL, $this->loganalyzer->setHandleToOffset(NULL, NULL, NULL));
	}

	/**
	 * @test
	 * @return void
	 */
	public function setHandleToOffsetWithoutOffsetAndComparehashTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1');
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals($handle, $this->loganalyzer->setHandleToOffset($handle, NULL, NULL));
		fclose($handle);
	}

	/**
	 * @test
	 * @return void
	 */
	public function setHandleToOffsetWithOffsetAndComparehashTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3' . PHP_EOL . 'line4' . PHP_EOL);
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals(18, ftell($this->loganalyzer->setHandleToOffset($handle, 12, md5('line3' . PHP_EOL))));
		fclose($handle);
	}

	/**
	 * @test
	 * @return void
	 */
	public function setHandleToOffsetWithOffsetAndWrongComparehashTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3' . PHP_EOL . 'line4' . PHP_EOL);
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals(0, ftell($this->loganalyzer->setHandleToOffset($handle, 12, md5('line2' . PHP_EOL))));
		fclose($handle);
	}

	/**
	 * @test
	 * @return void
	 */
	public function setHandleToOffsetWithWrongOffsetTest() {
		$file = vfsStream::url('var/logfile.log');
		file_put_contents($file, 'line1' . PHP_EOL . 'line2' . PHP_EOL . 'line3' . PHP_EOL . 'line4' . PHP_EOL);
		$handle = fopen(vfsStream::url('var/logfile.log'), 'r');
		$this->assertEquals(0, ftell($this->loganalyzer->setHandleToOffset($handle, 99, md5('line2' . PHP_EOL))));
		fclose($handle);
	}

	/**
	 * @test
	 * @expectedException \Derhansen\Quixr\Exceptions\AnalyzeLogfileException
	 * @return void
	 */
	public function analyzeLogfileReturnsExceptionIfLogfileNotFound() {
		$vhostData = $this->getDefaultVhostData();
		$this->loganalyzer->analyzeLogfile(vfsStream::url('var/logfile.log'), $vhostData);
	}

	/**
	 * @test
	 * @return void
	 */
	public function analyzeLogfileReturnsExpectedTrafficDataForGivenCommonLogfileWithNoHistoricalData() {
		$vhostData = $this->getDefaultVhostData();
		$this->loganalyzer->setLogformat('common');
		$result = $this->loganalyzer->analyzeLogfile(__DIR__ . '/../Fixtures/Logfiles/common.log', $vhostData);
		$expected = array(
			'vhost1' => array(
				'traffic' => array(
					'2014' => array(
						'2' => array(
							'2' => 12288
						)
					)
				),
				'quixr' => array(
					'traffic_lasttstamp' => 1391342760,
					'traffic_lastoffset' => 858,
					'traffic_lastlinehash' => '2dd22936f7de0eeea924e366547c878e'
				),
			)
		);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function analyzeLogfileReturnsExpectedTrafficDataForGivenCombinedLogfileWithNoHistoricalData() {
		$vhostData = $this->getDefaultVhostData();
		$result = $this->loganalyzer->analyzeLogfile(__DIR__ . '/../Fixtures/Logfiles/combined.log', $vhostData);
		$expected = array(
			'vhost1' => array(
				'traffic' => array(
					'2014' => array(
						'2' => array(
							'2' => 12288
						)
					)
				),
				'quixr' => array(
					'traffic_lasttstamp' => 1391342760,
					'traffic_lastoffset' => 1573,
					'traffic_lastlinehash' => 'e973361fbe96a50ade0d8a3c705c4606'
				),
			)
		);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function analyzeLogfileReturnsExpectedTrafficDataForGivenCommonLogfileWithHistoricalData() {
		$vhostData = $this->getDefaultVhostData();
		$vhostData['vhost1']['traffic']['2014']['2']['1'] = 1024;
		$vhostData['vhost1']['traffic']['2014']['2']['2'] = 1024;
		$this->loganalyzer->setLogformat('common');
		$result = $this->loganalyzer->analyzeLogfile(__DIR__ . '/../Fixtures/Logfiles/common.log', $vhostData);
		$expected = array(
			'vhost1' => array(
				'traffic' => array(
					'2014' => array(
						'2' => array(
							'1' => 1024,
							'2' => 13312
						)
					)
				),
				'quixr' => array(
					'traffic_lasttstamp' => 1391342760,
					'traffic_lastoffset' => 858,
					'traffic_lastlinehash' => '2dd22936f7de0eeea924e366547c878e'
				),
			)
		);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function analyzeLogfileReturnsExpectedTrafficDataForGivenCombinedLogfileWithHistoricalData() {
		$vhostData = $this->getDefaultVhostData();
		$vhostData['vhost1']['traffic']['2014']['2']['1'] = 1024;
		$vhostData['vhost1']['traffic']['2014']['2']['2'] = 1024;
		$result = $this->loganalyzer->analyzeLogfile(__DIR__ . '/../Fixtures/Logfiles/combined.log', $vhostData);
		$expected = array(
			'vhost1' => array(
				'traffic' => array(
					'2014' => array(
						'2' => array(
							'1' => 1024,
							'2' => 13312
						)
					)
				),
				'quixr' => array(
					'traffic_lasttstamp' => 1391342760,
					'traffic_lastoffset' => 1573,
					'traffic_lastlinehash' => 'e973361fbe96a50ade0d8a3c705c4606'
				),
			)
		);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function analyzeLogfileReturnsExpectedTrafficDataForGivenCommonLogfileWithLasttstamp() {
		$vhostData = $this->getDefaultVhostData();
		$vhostData['vhost1']['quixr']['traffic_lasttstamp'] = 1391342340;
		$this->loganalyzer->setLogformat('common');
		$result = $this->loganalyzer->analyzeLogfile(__DIR__ . '/../Fixtures/Logfiles/common.log', $vhostData);
		$expected = array(
			'vhost1' => array(
				'traffic' => array(
					'2014' => array(
						'2' => array(
							'2' => 7168
						)
					)
				),
				'quixr' => array(
					'traffic_lasttstamp' => 1391342760,
					'traffic_lastoffset' => 858,
					'traffic_lastlinehash' => '2dd22936f7de0eeea924e366547c878e'
				),
			)
		);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function analyzeLogfileReturnsExpectedTrafficDataForGivenCombinedLogfileWithLasttstamp() {
		$vhostData = $this->getDefaultVhostData();
		$vhostData['vhost1']['quixr']['traffic_lasttstamp'] = 1391342340;
		$result = $this->loganalyzer->analyzeLogfile(__DIR__ . '/../Fixtures/Logfiles/combined.log', $vhostData);
		$expected = array(
			'vhost1' => array(
				'traffic' => array(
					'2014' => array(
						'2' => array(
							'2' => 7168
						)
					)
				),
				'quixr' => array(
					'traffic_lasttstamp' => 1391342760,
					'traffic_lastoffset' => 1573,
					'traffic_lastlinehash' => 'e973361fbe96a50ade0d8a3c705c4606'
				),
			)
		);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getEmptyVhostDataReturnsExpectedArrayTest() {
		$expected = array(
			'test' => array(
				'traffic' => array(),
				'quixr' => array(
					'traffic_lasttstamp' => 0,
					'traffic_lastoffset' => -1,
					'traffic_lastlinehash' => ''
				),
			)
		);
		$this->assertSame($expected, $this->loganalyzer->getEmptyVhostData('test'));
	}

	/**
	 * Returns default vhost data
	 *
	 * @return array
	 */
	private function getDefaultVhostData() {
		return array(
			'vhost1' => array(
				'traffic' => array(),
				'quixr' => array(
					'traffic_lasttstamp' => -1,
					'traffic_lastoffset' => 0,
					'traffic_lastlinehash' => ''
				),
			)
		);
	}

}