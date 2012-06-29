<?php
use Klei\Phut\Test;
use Klei\Phut\TestFixture;
use Klei\Phut\Assert;
use Klei\Phut\Timer;

/**
 * @TestFixture
 */
class TimerTests {
	/**
 	 * @Test
 	 */
	public function isRunning_NeverStarted_EqualsFalse() {
		// Given
		$timer = new Timer();

		// Then
		Assert::isFalse($timer->isRunning());
	}

	/**
 	 * @Test
 	 */
	public function isRunning_StartedNeverStopped_EqualsTrue() {
		// Given
		$timer = new Timer();
		$timer->start();

		// Then
		Assert::isTrue($timer->isRunning());
	}

	/**
	 * @Test
	 */
	public function isRunning_StartedAndStopped_EqualsFalse() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();

		// Then
		Assert::isFalse($timer->isRunning());
	}

	/**
	 * @Test
	 */
	public function hasBeenRun_NeverStarted_EqualsFalse() {
		// Given
		$timer = new Timer();

		// Then
		Assert::isFalse($timer->hasBeenRun());
	}

	/**
 	 * @Test
 	 */
	public function hasBeenRun_StartedNeverStopped_EqualsFalse() {
		// Given
		$timer = new Timer();
		$timer->start();

		// Then
		Assert::isFalse($timer->hasBeenRun());
	}

	/**
	 * @Test
	 */
	public function hasBeenRun_StartedAndStopped_EqualsTrue() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();

		// Then
		Assert::isTrue($timer->hasBeenRun());
	}

	/**
	 * @Test
	 */
	public function stop_NotStarted_ThrowException() {
		// Given
		$timer = new Timer();

		// When
		$toTest = function() use($timer) {
			$timer->stop();
		};

		// Then
		Assert::throws('Exception', $toTest);
	}

	/**
	 * @Test
	 */
	public function stop_Started_DoNotThrowException() {
		// Given
		$timer = new Timer();
		$timer->start();

		// When
		$toTest = function() use($timer) {
			$timer->stop();
		};

		// Then
		Assert::doesNotThrow($toTest);
	}

	/**
	 * @Test
	 */
	public function stop_SubsequentCalls_ThrowException() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();

		// When
		$toTest = function() use($timer) {
			$timer->stop();
		};

		// Then
		Assert::throws('Exception', $toTest);
	}

	/**
	 * @Test
	 */
	public function getElapsedTime_NotStarted_ThrowException() {
		// Given
		$timer = new Timer();

		// When
		$toTest = function() use($timer) {
			$timer->getElapsedTime();
		};

		// Then
		Assert::throws('Exception', $toTest);
	}

	/**
	 * @Test
	 */
	public function getElapsedTime_Started_DoNotThrowException() {
		// Given
		$timer = new Timer();
		$timer->start();

		// When
		$toTest = function() use($timer) {
			$timer->getElapsedTime();
		};

		// Then
		Assert::doesNotThrow($toTest);
	}

	/**
	 * @Test
	 */
	public function getElapsedTime_SubsequentCallsWhenStopped_ResultsShouldBeIdentical() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();
		$elapsedTime = $timer->getElapsedTime();
		usleep(1000);

		// When
		$newElapsedTime = $timer->getElapsedTime();

		// Then
		Assert::areIdentical($newElapsedTime, $elapsedTime);
	}

	/**
	 * @Test
	 */
	public function getElapsedTime_SubsequentCallsNotStopped_ResultsShouldDiffer() {
		// Given
		$timer = new Timer();
		$timer->start();
		$elapsedTime = $timer->getElapsedTime();
		usleep(1000);

		// When
		$newElapsedTime = $timer->getElapsedTime();

		// Then
		Assert::areNotIdentical($newElapsedTime, $elapsedTime);
		Assert::greater($newElapsedTime, $elapsedTime);
	}
}