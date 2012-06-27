<?php
use Klei\Phut\TestFixture;
use Klei\Phut\Test;
use Klei\Phut\TestCase;
use Klei\Phut\Assert;
use Klei\Phut\AssertionException;
use Klei\Phut\Model\TestMethod;

/**
 * @TestFixture
 */
class TestMethodTests {
	/**
	 * @Test
	 */
	public function NewTestMethod_TargetClassDoesNotContainMethod_ThrowInvalidArgumentException() {
		// Given
		$targetClass = new stdClass();
		$method = new \ReflectionMethod(__METHOD__);

		// When
		$toTest = function() use($targetClass, $method) {
			new TestMethod($targetClass, $method);
		};

		// Then
		Assert::throws('InvalidArgumentException', $toTest);
	}

	/**
	 * @Test
	 */
	public function NewTestMethod_TargetClassIsNotAnObject_ThrowInvalidArgumentException() {
		// Given
		$targetClass = "NOT AN OBJECT";
		$method = new \ReflectionMethod(__METHOD__);

		// When
		$toTest = function() use($targetClass, $method) {
			new TestMethod($targetClass, $method);
		};

		// Then
		Assert::throws('InvalidArgumentException', $toTest);
	}

	/**
	 * @Test
	 */
	public function NewTestMethod_TargetMethodHasRequiredParamsNoTestCaseGiven_ThrowInvalidArgumentException() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod');

		// When
		$toTest = function() use($targetClass, $method) {
			new TestMethod($targetClass, $method);
		};

		// Then
		Assert::throws('InvalidArgumentException', $toTest);
	}

	/**
	 * @Test
	 */
	public function NewTestMethod_TestCaseWithTooFewParamsGiven_ThrowInvalidArgumentException() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod');
		$testCaseParameters = array();
		$testCase = new TestCase($testCaseParameters);

		// When
		$toTest = function() use($targetClass, $method, $testCase) {
			new TestMethod($targetClass, $method, $testCase);
		};

		// Then
		Assert::throws('InvalidArgumentException', $toTest);
	}

	/**
	 * @Test
	 */
	public function NewTestMethod_TestCaseWithCorrectNumberOfParamsGiven_ShouldNotThrowException() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod');
		$testCaseParameters = array("One param");
		$testCase = new TestCase($testCaseParameters);

		// When
		$toTest = function() use($targetClass, $method, $testCase) {
			new TestMethod($targetClass, $method, $testCase);
		};

		// Then
		Assert::doesNotThrow($toTest);
	}

	/**
	 * @Test
	 */
	public function isParameterizedTest_TestCaseWithParams_EqualsTrue() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod');
		$testCaseParameters = array("One param");
		$testCase = new TestCase($testCaseParameters);
		$testMethod = new TestMethod($targetClass, $method, $testCase);

		// Then
		Assert::isTrue($testMethod->isParameterizedTest());
	}

	/**
	 * @Test
	 */
	public function isParameterizedTest_NoTestCase_EqualsFalse() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod2');
		$testMethod = new TestMethod($targetClass, $method);

		// Then
		Assert::isFalse($testMethod->isParameterizedTest());
	}

	/**
	 * @Test
	 */
	public function run_ParameterizedTestWithoutError_MethodShouldBeInvokedAndResultNull() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod');
		$testCaseParameters = array("One param");
		$testCase = new TestCase($testCaseParameters);
		$testMethod = new TestMethod($targetClass, $method, $testCase);

		// When
		$result = $testMethod->run();

		// Then
		Assert::isTrue($targetClass->testMethodHasBeenRun);
		Assert::isNull($result);
	}

	/**
	 * @Test
	 */
	public function run_OrdinaryTestWithoutError_MethodShouldBeInvokedAndResultNull() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod2');
		$testMethod = new TestMethod($targetClass, $method);

		// When
		$result = $testMethod->run();

		// Then
		Assert::isTrue($targetClass->testMethod2HasBeenRun);
		Assert::isNull($result);
	}


	/**
	 * @Test
	 */
	public function run_ParameterizedTestThrowsAssertionException_ResultShouldEqualExceptionMessage() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethodWithException');
		$testCaseParameters = array("One param");
		$testCase = new TestCase($testCaseParameters);
		$testMethod = new TestMethod($targetClass, $method, $testCase);

		// When
		$result = $testMethod->run();

		// Then
		Assert::areIdentical($result, $targetClass::EXCEPTION_MESSAGE);
	}

	/**
	 * @Test
	 */
	public function run_OrdinaryTestThrowsAssertionException_ResultShouldEqualExceptionMessage() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod2WithException');
		$testMethod = new TestMethod($targetClass, $method);

		// When
		$result = $testMethod->run();

		// Then
		Assert::areIdentical($result, $targetClass::EXCEPTION_MESSAGE);
	}
}

class TestTargetClass {
	const EXCEPTION_MESSAGE = "THE EXCEPTION MESSAGE";
	public $testMethodHasBeenRun = false;
	public $testMethod2HasBeenRun = false;

	public function testMethod($param1) {
		$this->testMethodHasBeenRun = true;
		return;
	}

	public function testMethodWithException($param1) {
		throw new AssertionException(self::EXCEPTION_MESSAGE);
		return;
	}

	public function testMethod2() {
		$this->testMethod2HasBeenRun = true;
		return;
	}

	public function testMethod2WithException() {
		throw new AssertionException(self::EXCEPTION_MESSAGE);
		return;
	}
}