<?php
use Klei\Phut\TestFixture;
use Klei\Phut\Test;
use Klei\Phut\Assert;
use Klei\Phut\TestCase;

/**
 * @TestFixture
 */
class TestCaseTests {
	/**
	 * @Test
	 */
	public function hasParams_TestCaseInstantiatedWithNonEmptyArray_EqualsTrue() {
		// Given
		$input = array("NON EMPTY");
		$testCase = new TestCase($input);

		// When
		$hasParams = $testCase->hasParams();

		// Then
		Assert::isTrue($hasParams);
	}

	/**
	 * @Test
	 */
	public function hasParams_TestCaseInstantiatedWithEmptyArray_EqualsFalse() {
		// Given
		$input = array();
		$testCase = new TestCase($input);

		// When
		$hasParams = $testCase->hasParams();

		// Then
		Assert::isFalse($hasParams);
	}

	/**
	 * @Test
	 */
	public function getParams_TestCaseInstantiatedWithAnArray_EqualsInputArray() {
		// Given
		$input = array("AN ARRAY");
		$testCase = new TestCase($input);

		// When
		$output = $testCase->getParams();

		// Then
		Assert::areIdentical($output, $input);
	}
}