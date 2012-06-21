<?php
namespace Klei\Phut;

class Assert {
	public static function isTrue($value) {
		if ((bool)$value !== true) {
			throw new AssertionException('Assertion failed. Expected: true, But was: false');
		}
	}

	public static function isFalse($value) {
		if ((bool)$value !== false) {
			throw new AssertionException('Assertion failed. Expected: false, But was: true');
		}
	}

	public static function isNull($value) {
		if ($value !== null) {
			throw new AssertionException(sprintf('Assertion failed. Expected: NULL, But was: %s', gettype($value)));
		}
	}

	public static function isNotNull($value) {
		if ($value === null) {
			throw new AssertionException('Assertion failed. Expected: NOT NULL, But was: NULL');
		}
	}

	public static function areEqual($actual, $expected) {
		if ($actual != $expected) {
			throw new AssertionException(sprintf('Assertion failed. Expected: %s, But was: %s', $expected, $actual));
		}
	}

	public static function areIdentical($actual, $expected) {
		if ($actual !== $expected) {
			throw new AssertionException(sprintf('Assertion failed. Expected: %s, But was: %s', $expected, $actual));
		}
	}

	public static function isEmpty($value) {
		if (is_string($value) && strlen($value) !== 0) {
			throw new AssertionException(sprintf('Assertion failed. Expected an empty string, But was: %s', $value));
		}
		if (is_array($value) && !empty($value)) {
			throw new AssertionException(sprintf('Assertion failed. Expected an empty array, But was: array(%d)', count($value)));
		}
		if ($value instanceof \Traversable) {
			$value->rewind();
			$count = iterator_count($value);
			if ($count !== 0) {
				throw new AssertionException(sprintf('Assertion failed. Expeced an empty iterator, But was: %s(%d)', gettype($value), $count));
			}
		}
		self::failIfNeitherStringNorArrayNorIterator($value);
	}

	public static function isNotEmpty($value) {
		if (is_string($value) && strlen($value) === 0) {
			throw new AssertionException('Assertion failed. Expected anything but an empty string, But was: empty string');
		}
		if (is_array($value) && empty($value)) {
			throw new AssertionException('Assertion failed. Expected a non empty array, But was: empty array');
		}
		if ($value instanceof \Traversable) {
			$value->rewind();
			$count = iterator_count($value);
			if ($count === 0) {
				throw new AssertionException(sprintf('Assertion failed. Expeced a non empty iterator, But was: empty %s', gettype($value)));
			}
		}
		self::failIfNeitherStringNorArrayNorIterator($value);
	}

	protected static function failIfNeitherStringNorArrayNorIterator($value) {
		if (!is_string($value) && !is_array($value) && !($value instanceof \Traversable)) {
			throw new \InvalidArgumentException(sprintf('Parameter $value is expected to be either a string, an array or an iterator, But was: %s', gettype($value)));
		}
	}

	public static function throws($expectedException, $callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException(sprintf('Parameter $callable is expected to be callable, But was: %s', gettype($callable)));
		}
		try {
			$callable();
		} catch (\Exception $e) {
			$actualException = get_class($e);
			if ($expectedException !== $actualException) {
				throw new AssertionException(sprintf('Assertion failed. Expected exception: %s, But was: %s', $expectedException, $actualException));
			} else {
				return;
			}
		}
		throw new AssertionException(sprintf('Assertion failed. Expected exception: %s, But no exception was thrown', $expectedException));
	}
}
?>