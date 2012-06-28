<?php
use Klei\Phut\Test;
use Klei\Phut\TestFixture;
use Klei\Phut\Assert;
use Klei\Phut\Setup;
use Klei\Phut\Teardown;
use Klei\Phut\MethodHandler;

/**
 * @TestFixture
 */
class MethodHandlerTests {
	/**
 	 * @Test
 	 */
	public function isSetupMethod_MethodHasSetupAnnotation_EqualsTrue() {
		// Given
		$method = new \ReflectionMethod('TestClass', 'Setup');
		$handler = new MethodHandler();

		// Then
		Assert::isTrue($handler->isSetupMethod($method));
	}

	/**
 	 * @Test
 	 */
	public function isSetupMethod_MethodDoesNotHaveSetupAnnotation_EqualsFalse() {
		// Given
		$method = new \ReflectionMethod('TestClass', 'NoAnnotation');
		$handler = new MethodHandler();

		// Then
		Assert::isFalse($handler->isSetupMethod($method));
	}

	/**
 	 * @Test
 	 */
	public function isTestMethod_MethodHasTestAnnotation_EqualsTrue() {
		// Given
		$method = new \ReflectionMethod('TestClass', 'Test');
		$handler = new MethodHandler();

		// Then
		Assert::isTrue($handler->isTestMethod($method));
	}

	/**
 	 * @Test
 	 */
	public function isTestMethod_MethodDoesNotHaveTestAnnotation_EqualsFalse() {
		// Given
		$method = new \ReflectionMethod('TestClass', 'NoAnnotation');
		$handler = new MethodHandler();

		// Then
		Assert::isFalse($handler->isTestMethod($method));
	}

	/**
 	 * @Test
 	 */
	public function isTeardownMethod_MethodHasTeardownAnnotation_EqualsTrue() {
		// Given
		$method = new \ReflectionMethod('TestClass', 'Teardown');
		$handler = new MethodHandler();

		// Then
		Assert::isTrue($handler->isTeardownMethod($method));
	}

	/**
 	 * @Test
 	 */
	public function isTeardownMethod_MethodDoesNotHaveTeardownAnnotation_EqualsFalse() {
		// Given
		$method = new \ReflectionMethod('TestClass', 'NoAnnotation');
		$handler = new MethodHandler();

		// Then
		Assert::isFalse($handler->isTeardownMethod($method));
	}

	/**
	 * @Test
	 */
	public function getMethods_FromClassName_ReturnReflectionMethods() {
		// Given
		$handler = new MethodHandler();

		// When
		$methods = $handler->getMethods('TestClass');

		// Then
		foreach ($methods as $method) {
			Assert::isTrue($method instanceof \ReflectionMethod);
		}
	}

	/**
	 * @Test
	 */
	public function getMethods_FromClassName_ReturnAllMethodsInClass() {
		// Given
		$handler = new MethodHandler();

		// When
		$methodNames = array_map(function($method) {
			return $method->getName();
		}, $handler->getMethods('TestClass'));

		// Then
		Assert::areIdentical($methodNames, array('Setup', 'Test', 'Teardown', 'NoAnnotation'));
	}

	/**
	 * @Test
	 */
	public function extractSetupMethod_SetupMethodExists_ReturnIt() {
		// Given
		$methods = array(new \ReflectionMethod('TestClass', 'Setup'));
		$handler = new MethodHandler();

		// When
		$setup = $handler->extractSetupMethod($methods);

		// Then
		Assert::areIdentical($setup, $methods[0]);
	}

	/**
	 * @Test
	 */
	public function extractSetupMethod_NoSetupMethodExists_ReturnNull() {
		// Given
		$methods = array();
		$handler = new MethodHandler();

		// When
		$setup = $handler->extractSetupMethod($methods);

		// Then
		Assert::isNull($setup);
	}

	/**
	 * @Test
	 */
	public function extractSetupMethod_TooManySetupMethodsExists_ThrowException() {
		// Given
		$methods = array(new \ReflectionMethod('TestClass', 'Setup'), new \ReflectionMethod('TestClass', 'Setup'));
		$handler = new MethodHandler();

		// When
		$toTest = function() use($handler, $methods) {
			$handler->extractSetupMethod($methods);
		};

		// Then
		Assert::throws('Exception', $toTest);
	}

	/**
	 * @Test
	 */
	public function extractTestMethods_TestMethodsExists_ReturnThem() {
		// Given
		$methods = array(new \ReflectionMethod('TestClass', 'Test'));
		$handler = new MethodHandler();

		// When
		$tests = $handler->extractTestMethods($methods);

		// Then
		Assert::areIdentical($tests, $methods);
	}

	/**
	 * @Test
	 */
	public function extractTestMethods_NoTestMethodsExists_ThrowException() {
		// Given
		$methods = array();
		$handler = new MethodHandler();

		// When
		$toTest = function() use($handler, $methods) {
			$handler->extractTestMethods($methods);
		};

		// Then
		Assert::throws('Exception', $toTest);
	}

	/**
	 * @Test
	 */
	public function extractTeardownMethod_TeardownMethodExists_ReturnIt() {
		// Given
		$methods = array(new \ReflectionMethod('TestClass', 'Teardown'));
		$handler = new MethodHandler();

		// When
		$teardown = $handler->extractTeardownMethod($methods);

		// Then
		Assert::areIdentical($teardown, $methods[0]);
	}

	/**
	 * @Test
	 */
	public function extractTeardownMethod_NoTeardownMethodExists_ReturnNull() {
		// Given
		$methods = array();
		$handler = new MethodHandler();

		// When
		$teardown = $handler->extractTeardownMethod($methods);

		// Then
		Assert::isNull($teardown);
	}

	/**
	 * @Test
	 */
	public function extractTeardownMethod_TooManyTeardownMethodsExists_ThrowException() {
		// Given
		$methods = array(new \ReflectionMethod('TestClass', 'Teardown'), new \ReflectionMethod('TestClass', 'Teardown'));
		$handler = new MethodHandler();

		// When
		$toTest = function() use($handler, $methods) {
			$handler->extractTeardownMethod($methods);
		};

		// Then
		Assert::throws('Exception', $toTest);
	}
}

class TestClass {
	/**
	 * @Setup
	 */
	public function Setup() {
		
	}

	/**
	 * @Test
	 */
	public function Test() {
		
	}

	/**
	 * @Teardown
	 */
	public function Teardown() {
		
	}

	public function NoAnnotation() {
		
	}
}