<?php
use Klei\Phut\Test;
use Klei\Phut\TestFixture;
use Klei\Phut\Assert;
use Klei\Phut\Timer;
use Klei\Phut\AssertionException;
use Klei\Phut\Model\MethodResult;

/**
 * @TestFixture
 */
class MethodResultTests {
	/**
 	 * @Test
 	 */
	public function newMethodResult_ValidTimerNoException_DoesNotThrow() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();

		// When
		$toTest = function() use($timer) {
			new MethodResult($timer);
		};

		// Then
		Assert::doesNotThrow($toTest);
	}

	/**
 	 * @Test
 	 */
	public function newMethodResult_InvalidTimerNoException_ThrowsInvalidArgumentException() {
		// Given
		$timer = new Timer();

		// When
		$toTest = function() use($timer) {
			new MethodResult($timer);
		};

		// Then
		Assert::throws('InvalidArgumentException', $toTest);
	}

	/**
 	 * @Test
 	 */
	public function newMethodResult_ValidTimerWithException_DoesNotThrow() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();
		$exception = new \Exception("YADA");

		// When
		$toTest = function() use($timer, $exception) {
			new MethodResult($timer, $exception);
		};

		// Then
		Assert::doesNotThrow($toTest);
	}

	/**
 	 * @Test
 	 */
	public function newMethodResult_WithException_IsNotSuccessful() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();
		$exception = new \Exception("YADA");
		$methodResult = new MethodResult($timer, $exception);

		// Then
		Assert::isFalse($methodResult->isSuccessful());
	}

	/**
 	 * @Test
 	 */
	public function newMethodResult_WithoutException_IsSuccessful() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();
		$methodResult = new MethodResult($timer);

		// Then
		Assert::isTrue($methodResult->isSuccessful());
	}

	/**
 	 * @Test
 	 */
	public function newMethodResult_WithAssertionException_IsNotUnexpectedException() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();
		$exception = new AssertionException("YADA");
		$methodResult = new MethodResult($timer, $exception);

		// Then
		Assert::isFalse($methodResult->isUnexpectedException());
	}

	/**
 	 * @Test
 	 */
	public function newMethodResult_WithNoAssertionException_IsUnexpectedException() {
		// Given
		$timer = new Timer();
		$timer->start();
		$timer->stop();
		$exception = new \Exception("YADA");
		$methodResult = new MethodResult($timer, $exception);

		// Then
		Assert::isTrue($methodResult->isUnexpectedException());
	}
}