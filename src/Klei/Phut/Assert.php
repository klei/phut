<?php
namespace Klei\Phut;

class Assert {
	public static function isTrue($value) {
		if ((bool)$value !== true)
			throw new AssertionException('Assertion failed. Expected: true, But was: false');
	}

	public static function isFalse($value) {
		if ((bool)$value !== false)
			throw new AssertionException('Assertion failed. Expected: false, But was: true');
	}

	public static function isNull($value) {
		if ($value !== null)
			throw new AssertionException(sprintf('Assertion failed. Expected: NULL, But was: %s', gettype($value)));
	}

	public static function isNotNull($value) {
		if ($value === null)
			throw new AssertionException('Assertion failed. Expected: NOT NULL, But was: NULL');
	}

	public static function areEqual($actual, $expected) {
		if ($actual != $expected)
			throw new AssertionException(sprintf('Assertion failed. Expected: %s, But was: %s', $expected, $actual));
	}

	public static function areIdentical($actual, $expected) {
		if ($actual !== $expected)
			throw new AssertionException(sprintf('Assertion failed. Expected: %s, But was: %s', $expected, $actual));
	}
}
?>