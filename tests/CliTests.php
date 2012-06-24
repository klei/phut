<?php
use Klei\Phut\Cli;
use Klei\Phut\Assert;
use Klei\Phut\TestFixture;
use Klei\Phut\Test;

/**
 * @TestFixture
 */
class CliTests {
	const COLOR_RESET = "\033[0m";
	/**
	 * @Test
	 */
	public function string_NoColorSpecified_InputEqualsOutput() {
		// Given
		$input = "Test text";
		$cli = new Cli();

		// When
		$output = $cli->string($input);

		// Then
		Assert::areIdentical($output, $input);
	}

	/**
	 * @Test
	 */
	public function string_WithBackgroundColor_ShouldEndWithColorReset() {
		// Given
		$input = "Test text";
		$cli = new Cli();
		$backgroundColor = 'black';

		// When
		$output = $cli->string($input, null, $backgroundColor);
		$end = substr($output, -4);

		// Then
		Assert::areIdentical($end, self::COLOR_RESET);
	}

	/**
	 * @Test
	 */
	public function string_WithForegroundColor_ShouldEndWithColorReset() {
		// Given
		$input = "Test text";
		$cli = new Cli();
		$foregroundColor = 'white';

		// When
		$output = $cli->string($input, $foregroundColor);
		$end = substr($output, -4);

		// Then
		Assert::areIdentical($end, self::COLOR_RESET);
	}

	/**
	 * @Test
	 */
	public function string_WithForegroundAndBackgroundColor_ShouldEndWithColorReset() {
		// Given
		$input = "Test text";
		$cli = new Cli();
		$foregroundColor = 'white';
		$backgroundColor = 'black';

		// When
		$output = $cli->string($input, $foregroundColor, $backgroundColor);
		$end = substr($output, -4);

		// Then
		Assert::areIdentical($end, self::COLOR_RESET);
	}

	/**
	 * @Test
	 */
	public function string_WithUnknownForegroundColor_ShouldThrowInvalidArgumentException() {
		// Given
		$input = "Test text";
		$cli = new Cli();
		$foregroundColor = 'weird color';

		// When
		$action = function() use($cli, $input, $foregroundColor) {
			$cli->string($input, $foregroundColor);
		};

		// Then
		Assert::throws("InvalidArgumentException", $action);
	}

	/**
	 * @Test
	 */
	public function string_WithUnknownBackgroundColor_ShouldThrowInvalidArgumentException() {
		// Given
		$input = "Test text";
		$cli = new Cli();
		$backgroundColor = 'weird color';

		// When
		$action = function() use($cli, $input, $backgroundColor) {
			$cli->string($input, null, $backgroundColor);
		};

		// Then
		Assert::throws("InvalidArgumentException", $action);
	}

	/**
	 * @Test
	 */
	public function string_ColoringDisabledBothForegroundAndBackgroundColorGiven_OutputEqualsInput() {
		// Given
		$input = "Test text";
		$cli = new Cli();
		$cli->disableColoring();
		$foregroundColor = 'white';
		$backgroundColor = 'black';

		// When
		$output = $cli->string($input, $foregroundColor, $backgroundColor);

		// Then
		Assert::areIdentical($output, $input);
	}
}
?>