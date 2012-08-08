<?php
namespace Klei\Phut;

class Assert {
	/**
	 * @param bool $value
	 */
	public static function isTrue($value) {
		if ((bool)$value !== true) {
			throw new AssertionException('Assertion failed. Expected: true, But was: false');
		}
	}

	/**
	 * @param bool $value
	 */
	public static function isFalse($value) {
		if ((bool)$value !== false) {
			throw new AssertionException('Assertion failed. Expected: false, But was: true');
		}
	}

	/**
	 * @param mixed $value
	 */
	public static function isNull($value) {
		if ($value !== null) {
			throw new AssertionException(sprintf('Assertion failed. Expected: NULL, But was: %s', gettype($value)));
		}
	}

	/**
	 * @param mixed $value
	 */
	public static function isNotNull($value) {
		if ($value === null) {
			throw new AssertionException('Assertion failed. Expected: NOT NULL, But was: NULL');
		}
	}

	/**
	 * @param mixed $actual
	 * @param mixed $expected
	 */
	public static function areEqual($actual, $expected) {
		if ($actual != $expected) {
			throw new AssertionException(sprintf('Assertion failed. Expected: %s, But was: %s', self::toString($expected), self::toString($actual)));
		}
	}

	/**
	 * @param mixed $actual
	 * @param mixed $expected
	 */
	public static function areNotEqual($actual, $expected) {
		if ($actual == $expected) {
			throw new AssertionException(sprintf('Assertion failed. Expected not to be equal to: %s, But it was.', self::toString($expected)));
		}
	}

	/**
	 * @param mixed $actual
	 * @param mixed $expected
	 */
	public static function areIdentical($actual, $expected) {
		if ($actual !== $expected) {
			throw new AssertionException(sprintf('Assertion failed. Expected: %s, But was: %s', self::toString($expected), self::toString($actual)));
		}
	}

	/**
	 * @param mixed $actual
	 * @param mixed $expected
	 */
	public static function areNotIdentical($actual, $expected) {
		if ($actual === $expected) {
			throw new AssertionException(sprintf('Assertion failed. Expected not to be identical to: %s, But it was.', self::toString($expected)));
		}
	}

	/**
	 * @param string|array|\Traversable $value
	 */
	public static function isEmpty($value) {
		if (self::isString($value) && strlen((string)$value) !== 0) {
			throw new AssertionException(sprintf('Assertion failed. Expected an empty string, But was: %s', (string)$value));
		}
		if (is_array($value) && !empty($value)) {
			throw new AssertionException(sprintf('Assertion failed. Expected an empty array, But was: %s', self::toString($value)));
		}
		if ($value instanceof \Traversable) {
			$value->rewind();
			$count = iterator_count($value);
			if ($count !== 0) {
				throw new AssertionException(sprintf('Assertion failed. Expected an empty iterator, But was: %s of size %d', gettype($value), $count));
			}
		}
		self::failIfNeitherStringNorArrayNorIterator($value);
	}

	/**
	 * @param string|array|\Traversable $value
	 */
	public static function isNotEmpty($value) {
		if (self::isString($value) && strlen((string)$value) === 0) {
			throw new AssertionException('Assertion failed. Expected anything but an empty string, But was: empty string');
		}
		if (is_array($value) && empty($value)) {
			throw new AssertionException('Assertion failed. Expected a non empty array, But was: empty array');
		}
		if ($value instanceof \Traversable) {
			$value->rewind();
			$count = iterator_count($value);
			if ($count === 0) {
				throw new AssertionException(sprintf('Assertion failed. Expected a non empty iterator, But was: empty %s', gettype($value)));
			}
		}
		self::failIfNeitherStringNorArrayNorIterator($value);
	}

	/**
	 * Asserts that the callback does throw an exception of the given type
	 *
	 * @param string $expectedException
	 * @param callable $callable
	 */
	public static function throws($expectedException, $callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException(sprintf('Parameter $callable is expected to be callable, But was: %s', gettype($callable)));
		}
		try {
			$callable();
		} catch (\Exception $e) {
			$actualException = get_class($e);
			if ($expectedException !== $actualException) {
				throw new AssertionException(
					sprintf(
						'Assertion failed. Expected exception: %s, But was: %s ("%s", Line: %d, File: %s)',
						$expectedException,
						$actualException,
						$e->getMessage(),
						$e->getLine(),
						$e->getFile()
					)
				); // @TODO: Add InnerException parameter to AssertionException, so the message could be formatted later
			} else {
				return $e;
			}
		}
		throw new AssertionException(sprintf('Assertion failed. Expected exception: %s, But no exception was thrown', $expectedException));
	}

	/**
	 * Asserts that the callback does not throw an exception
	 *
	 * @param callable $callable Callback
	 */
	public static function doesNotThrow($callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException(sprintf('Parameter $callable is expected to be callable, But was: %s', gettype($callable)));
		}
		try {
			$callable();
		} catch (\Exception $e) {
			throw new AssertionException(
				sprintf(
					'Assertion failed. Expected no thrown exception, But was: %s  ("%s", Line: %d, File: %s)',
					get_class($e),
					$e->getMessage(),
					$e->getLine(),
					$e->getFile()
				)
			);  // @TODO: Add InnerException parameter to AssertionException, so the message could be formatted later
		}
	}

	/**
	 * Asserts that $value1 is greater than $value2
	 *
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return void
	 */
	public static function greater($value1, $value2) {
		if (self::isString($value1) && self::isString($value2)) {
			if (strcmp($value1, $value2) <= 0) {
				throw new AssertionException(sprintf('Assertion failed. Expected "%s" to be greater than "%s".', (string)$value1, (string)$value2));
			}
			return;
		} elseif (gettype($value1) !== gettype($value2)) {
			throw new \InvalidArgumentException(sprintf('Expected $value1 and $value2 to be of same type, But was: %s, and %s respectively', gettype($value1), gettype($value2)));
		}
		if ($value1 <= $value2) {
			throw new AssertionException(sprintf('Assertion failed. Expected %s to be greater than %s', $value1, $value2));
		}
	}

	/**
	 * Asserts that $value1 is greater than or equal to $value2
	 *
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return void
	 */
	public static function greaterOrEqual($value1, $value2) {
		if (self::isString($value1) && self::isString($value2)) {
			if (strcmp($value1, $value2) < 0) {
				throw new AssertionException(sprintf('Assertion failed. Expected "%s" to be greater than or equal to "%s".', (string)$value1, (string)$value2));
			}
			return;
		} elseif (gettype($value1) !== gettype($value2)) {
			throw new \InvalidArgumentException(sprintf('Expected $value1 and $value2 to be of same type, But was: %s, and %s respectively', gettype($value1), gettype($value2)));
		}
		if ($value1 < $value2) {
			throw new AssertionException(sprintf('Assertion failed. Expected %s to be greater than or equal to %s', $value1, $value2));
		}
	}

	/**
	 * Asserts that $value1 is less than $value2
	 *
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return void
	 */
	public static function less($value1, $value2) {
		if (self::isString($value1) && self::isString($value2)) {
			if (strcmp($value1, $value2) >= 0) {
				throw new AssertionException(sprintf('Assertion failed. Expected "%s" to be less than "%s".', (string)$value1, (string)$value2));
			}
			return;
		} elseif (gettype($value1) !== gettype($value2)) {
			throw new \InvalidArgumentException(sprintf('Expected $value1 and $value2 to be of same type, But was: %s, and %s respectively', gettype($value1), gettype($value2)));
		}
		if ($value1 >= $value2) {
			throw new AssertionException(sprintf('Assertion failed. Expected %s to be less than %s', $value1, $value2));
		}
	}

	/**
	 * Asserts that $value1 is less than or equal to $value2
	 *
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return void
	 */
	public static function lessOrEqual($value1, $value2) {
		if (self::isString($value1) && self::isString($value2)) {
			if (strcmp($value1, $value2) > 0) {
				throw new AssertionException(sprintf('Assertion failed. Expected "%s" to be less than or equal to "%s".', (string)$value1, (string)$value2));
			}
			return;
		} elseif (gettype($value1) !== gettype($value2)) {
			throw new \InvalidArgumentException(sprintf('Expected $value1 and $value2 to be of same type, But was: %s, and %s respectively', gettype($value1), gettype($value2)));
		}
		if ($value1 > $value2) {
			throw new AssertionException(sprintf('Assertion failed. Expected %s to be less than or equal to %s', $value1, $value2));
		}
	}

	/**
	 * @param string|array|\Traversable $value
	 */
	protected static function failIfNeitherStringNorArrayNorIterator($value) {
		if (!self::isString($value) && !is_array($value) && !($value instanceof \Traversable)) {
			throw new \InvalidArgumentException(sprintf('Parameter $value is expected to be either a string, an object implementing __toString(), an array or an iterator, But was: %s', gettype($value)));
		}
	}

	/**
	 * Checks if a variable is a string or an object implementing __toString()
	 *
	 * @param mixed $value
	 * @return bool
	 */
	protected static function isString($value) {
		if (is_string($value)) {
			return true;
		}
		if (is_object($value) && method_exists($value, '__toString')) {
			return true;
		}
		return false;
	}

	protected static function toString($value) {
		if (!is_array($value)) {
			return (string)$value;
		}
		return 'array(' . implode(', ', $value) . ')';
	}
}
?>