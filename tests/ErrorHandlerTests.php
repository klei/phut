<?php
use Klei\Phut\Test;
use Klei\Phut\TestFixture;
use Klei\Phut\Assert;
use Klei\Phut\ErrorHandler;

/**
 * @TestFixture
 */
class ErrorHandlerTests {
	/**
	 * @Test
	 */
	public function handler_AnyParameterValues_ShouldThrowErrorExceptionWithCorrectInfo() {
		// Given
		$errorMessage = "ERROR";
		$errorNo = E_USER_ERROR;
		$errorFile = "test.php";
		$errorLine = 1;
		$errorHandler = new ErrorHandler();

		// When
		$toTest = function() use($errorHandler, $errorNo, $errorMessage, $errorFile, $errorLine) {
			$errorHandler->handler($errorNo, $errorMessage, $errorFile, $errorLine);
		};

		// Then
		$exception = Assert::throws('ErrorException', $toTest);
		Assert::areIdentical($exception->getSeverity(), $errorNo);
		Assert::areIdentical($exception->getMessage(), $errorMessage);
		Assert::areIdentical($exception->getFile(), $errorFile);
		Assert::areIdentical($exception->getLine(), $errorLine);
	}

	/**
 	 * @Test
 	 */
	public function AnyMethod_TriggeringAnError_ShouldThrowErrorException() {
		// Given
		$anyMethod = function() {
			trigger_error("Oh, noes!", E_USER_ERROR);
		};

		// Then
		Assert::throws('ErrorException', $anyMethod);
	}
}