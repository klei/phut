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
		$method = new \ReflectionMethod($targetClass, 'testMethodWithParameter');

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
		$method = new \ReflectionMethod($targetClass, 'testMethodWithParameter');
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
		$method = new \ReflectionMethod($targetClass, 'testMethodWithParameter');
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
		$method = new \ReflectionMethod($targetClass, 'testMethodWithParameter');
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
		$method = new \ReflectionMethod($targetClass, 'testMethod');
		$testMethod = new TestMethod($targetClass, $method);

		// Then
		Assert::isFalse($testMethod->isParameterizedTest());
	}

	/**
	 * @Test
	 */
	public function run_ParameterizedTestWithoutError_MethodShouldBeInvokedAndResultSuccessful() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethodWithParameter');
		$testCaseParameters = array("One param");
		$testCase = new TestCase($testCaseParameters);
		$testMethod = new TestMethod($targetClass, $method, $testCase);

		// When
		$result = $testMethod->run();

		// Then
		Assert::isTrue($targetClass->testMethodHasBeenRun);
		Assert::isTrue($result->isSuccessful());
	}

	/**
	 * @Test
	 */
	public function run_OrdinaryTestWithoutError_MethodShouldBeInvokedAndResultSuccessful() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethod');
		$testMethod = new TestMethod($targetClass, $method);

		// When
		$result = $testMethod->run();

		// Then
		Assert::isTrue($targetClass->testMethod2HasBeenRun);
		Assert::isTrue($result->isSuccessful());
	}


	/**
	 * @Test
	 */
	public function run_ParameterizedTestThrowsAssertionException_ResultShouldNotBeSuccessful() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethodWithParameterAndException');
		$testCaseParameters = array("One param");
		$testCase = new TestCase($testCaseParameters);
		$testMethod = new TestMethod($targetClass, $method, $testCase);

		// When
		$result = $testMethod->run();

		// Then
		Assert::isFalse($result->isSuccessful());
	}

	/**
	 * @Test
	 */
	public function run_OrdinaryTestThrowsAssertionException_ResultShouldNotBeSuccessful() {
		// Given
		$targetClass = new TestTargetClass();
		$method = new \ReflectionMethod($targetClass, 'testMethodWithException');
		$testMethod = new TestMethod($targetClass, $method);

		// When
		$result = $testMethod->run();

		// Then
		Assert::isFalse($result->isSuccessful());
	}
}

class TestTargetClass {
	const EXCEPTION_MESSAGE = "THE EXCEPTION MESSAGE";
	public $testMethodHasBeenRun = false;
	public $testMethod2HasBeenRun = false;

	public function testMethodWithParameter($param1) {
		$this->testMethodHasBeenRun = true;
		return;
	}

	public function testMethodWithParameterAndException($param1) {
		throw new AssertionException(self::EXCEPTION_MESSAGE);
		return;
	}

	public function testMethod() {
		$this->testMethod2HasBeenRun = true;
		return;
	}

	public function testMethodWithException() {
		throw new AssertionException(self::EXCEPTION_MESSAGE);
		return;
	}
}