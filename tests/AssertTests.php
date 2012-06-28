<?php
use Klei\Phut\Assert;
use Klei\Phut\AssertionException;
use Klei\Phut\TestFixture;
use Klei\Phut\Test;

/**
 * @TestFixture
 */
class AssertTests {
	const EXPECTED_EXCEPTION_MESSAGE = 'Expected an AssertionException to be thrown, but none was.';

	/**
	 * @Test
	 */
	public function isTrue_ActualValueIsFalse_ShouldThrowAssertionException() {
		// Given
		$actual = false;

		// When
		$toTest = function() use($actual) {
			Assert::isTrue($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isTrue_ActualValueIsTrue_ShouldNotThrowAssertionException() {
		// Given
		$actual = true;

		// When
		$toTest = function() use($actual) {
			Assert::isTrue($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isFalse_ActualValueIsTrue_ShouldThrowAssertionException() {
		// Given
		$actual = true;

		// When
		$toTest = function() use($actual) {
			Assert::isFalse($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isFalse_ActualValueIsFalse_ShouldNotThrowAssertionException() {
		// Given
		$actual = false;

		// When
		$toTest = function() use($actual) {
			Assert::isFalse($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isNull_ActualValueIsNotNull_ShouldThrowAssertionException() {
		// Given
		$actual = "NOT NULL";

		// When
		$toTest = function() use($actual) {
			Assert::isNull($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isNull_ActualValueIsNull_ShouldNotThrowAssertionException() {
		// Given
		$actual = null;

		// When
		$toTest = function() use($actual) {
			Assert::isNull($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isNotNull_ActualValueIsNull_ShouldThrowAssertionException() {
		// Given
		$actual = null;

		// When
		$toTest = function() use($actual) {
			Assert::isNotNull($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isNotNull_ActualValueIsNotNull_ShouldNotThrowAssertionException() {
		// Given
		$actual = "NOT NULL";

		// When
		$toTest = function() use($actual) {
			Assert::isNotNull($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function areEqual_ActualAndExpectedIsTotallyDifferent_ShouldThrowAssertionException() {
		// Given
		$actual = 10;
		$expected = 20;

		// When
		$toTest = function() use($actual, $expected) {
			Assert::areEqual($actual, $expected);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function areEqual_ActualAndExpectedIsTheSameAccordingToPhp_ShouldNotThrowAssertionException() {
		// Given
		$actual = "10";
		$expected = 10;

		// When
		$toTest = function() use($actual, $expected) {
			Assert::areEqual($actual, $expected);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function areIdentical_ActualAndExpectedIsNotIdentical_ShouldThrowAssertionException() {
		// Given
		$actual = (int)10;
		$expected = (float)10;

		// When
		$toTest = function() use($actual, $expected) {
			Assert::areIdentical($actual, $expected);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function areIdentical_ActualAndExpectedIsIdentical_ShouldNotThrowAssertionException() {
		// Given
		$actual = (int)10;
		$expected = (int)10;

		// When
		$toTest = function() use($actual, $expected) {
			Assert::areIdentical($actual, $expected);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_NonEmptyString_ShouldThrowAssertionException() {
		// Given
		$actual = "NOT EMPTY STRING";

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_ObjectToStringReturnsNonEmptyString_ShouldThrowAssertionException() {
		// Given
		$actual = new TestObjectNonEmptyToString();

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_EmptyString_ShouldNotThrowAssertionException() {
		// Given
		$actual = "";

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_ObjectToStringReturnsEmptyString_ShouldNotThrowAssertionException() {
		// Given
		$actual = new TestObjectEmptyToString();

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_NonEmptyArray_ShouldThrowAssertionException() {
		// Given
		$actual = array("NOT EMPTY");

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_EmptyArray_ShouldNotThrowAssertionException() {
		// Given
		$actual = array();

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_NonEmptyIterator_ShouldThrowAssertionException() {
		// Given
		$actual = new \ArrayIterator(array("NOT EMPTY"));

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_EmptyIterator_ShouldNotThrowAssertionException() {
		// Given
		$actual = new \ArrayIterator(array());

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isEmpty_NoStringAndNoArrayAndNoIterator_ShouldThrowInvalidArgumentException() {
		// Given
		$actual = (int)-1;

		// When
		$toTest = function() use($actual) {
			Assert::isEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (\InvalidArgumentException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_EmptyString_ShouldThrowAssertionException() {
		// Given
		$actual = "";

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_ObjectToStringReturnsEmptyString_ShouldThrowAssertionException() {
		// Given
		$actual = new TestObjectEmptyToString();

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_NonEmptyString_ShouldNotThrowAssertionException() {
		// Given
		$actual = "NON EMPTY STRING";

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_ObjectToStringReturnsNonEmptyString_ShouldNotThrowAssertionException() {
		// Given
		$actual = new TestObjectNonEmptyToString();

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_EmptyArray_ShouldThrowAssertionException() {
		// Given
		$actual = array();

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_NonEmptyArray_ShouldNotThrowAssertionException() {
		// Given
		$actual = array("NON EMPTY");

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_EmptyIterator_ShouldThrowAssertionException() {
		// Given
		$actual = new \ArrayIterator(array());

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_NonEmptyIterator_ShouldNotThrowAssertionException() {
		// Given
		$actual = new \ArrayIterator(array("NON EMPTY"));

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function isNotEmpty_NoStringAndNoArrayAndNoIterator_ShouldThrowInvalidArgumentException() {
		// Given
		$actual = (int)-1;

		// When
		$toTest = function() use($actual) {
			Assert::isNotEmpty($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (\InvalidArgumentException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function throws_NoCallable_ShouldThrowInvalidArgumentException() {
		// Given
		$actual = "NOT CALLABLE";

		// When
		$toTest = function() use($actual) {
			Assert::throws("ANY", $actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (\InvalidArgumentException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function throws_CallableNotThrowingException_ShouldThrowAssertionException() {
		// Given
		$actual = function() {
			// Does not throw anything
			return;
		};

		// When
		$toTest = function() use($actual) {
			Assert::throws("ANY", $actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function throws_CallableThrowingExceptionNotEqualToExpected_ShouldThrowAssertionException() {
		// Given
		$actual = function() {
			throw new \BadMethodCallException("Wrong exception");
			return;
		};

		// When
		$toTest = function() use($actual) {
			Assert::throws("InvalidArgumentException", $actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}

	/**
	 * @Test
	 */
	public function throws_CallableThrowingCorrectException_ShouldNotThrowAssertionException() {
		// Given
		$actual = function() {
			throw new \BadMethodCallException("Right exception");
			return;
		};

		// When
		$toTest = function() use($actual) {
			Assert::throws("BadMethodCallException", $actual);
		};
		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function doesNotThrow_CallableNotThrowingException_ShouldNotThrowAssertionException() {
		// Given
		$actual = function() {
			// Does not throw anything
			return;
		};

		// When
		$toTest = function() use($actual) {
			Assert::doesNotThrow($actual);
		};

		// Then
		try {
			$toTest();
		} catch (AssertionException $ae) {
			// Not ok
			throw $ae;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * @Test
	 */
	public function doesNotThrow_CallableThrowingException_ShouldThrowAssertionException() {
		// Given
		$actual = function() {
			throw new \Exception("An exception");
			return;
		};

		// When
		$toTest = function() use($actual) {
			Assert::doesNotThrow($actual);
		};

		// Then
		$ok = false;
		try {
			$toTest();
		} catch (AssertionException $ae) {
			$ok = true;
		} catch (\Exception $e) {
			throw $e;
		}
		if (!$ok) {
			throw new AssertionException(self::EXPECTED_EXCEPTION_MESSAGE);
		}
	}
}

class TestObjectNonEmptyToString {
	public function __toString() {
		return "NON EMPTY";
	}
}

class TestObjectEmptyToString {
	public function __toString() {
		return "";
	}
}