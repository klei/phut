<?php
namespace Klei\Phut;

class ErrorHandler {
	/**
	 * Register method to register this error handler
	 *
	 * @return void
	 */
	public function register() {
		set_error_handler(array($this, 'handler'));
	}

	/**
	 * The actual error handler method
	 *
	 * @param int $errorNo Error severity number, usually a constant e.g. E_USER_ERROR
	 * @param string $errorMessage The error message
	 * @param string $errorFile Contains the filename that the error was raised in
	 * @param int $errLine Contains the line number the error was raised at
	 * @throws \ErrorException
	 * @return void
	 */
	public function handler($errorNo, $errorMessage, $errorFile, $errorLine) {
		throw new \ErrorException($errorMessage, 0, $errorNo, $errorFile, $errorLine);
	}
}