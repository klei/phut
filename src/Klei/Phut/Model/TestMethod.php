<?php
namespace Klei\Phut\Model;

use Klei\Phut\AssertionException;
use Klei\Phut\TestCase;

class TestMethod implements IRunnable {
	/**
	 * @var object
	 */
	protected $target;

	/**
	 * @var \ReflectionMethod
	 */
	protected $method;

	/**
	 * @var TestCase
	 */
	protected $testCase;

	/**
	 * @param object $targetClass
	 * @param \ReflectionMethod $method
	 * @param TestCase $testCase
	 */
	public function __construct($targetClass, \ReflectionMethod $method, TestCase $testCase = null) {
		if (!method_exists($targetClass, $method->getName())) {
			throw new \InvalidArgumentException(sprintf('Expected parameter $targetClass to have the method "%s", but it was not found on object of type: %s', $method->getName(), gettype($targetClass)));
		}
		if ($method->getNumberOfRequiredParameters() > 0) {
			if ($testCase === null) {
				throw new \InvalidArgumentException(sprintf('The test method %s::%s expects %d parameters, but no TestCase was specified', get_class($targetClass), $method->getName(), $method->getNumberOfRequiredParameters()));
			} elseif (count($testCase->getParams()) < $method->getNumberOfRequiredParameters()) {
				throw new \InvalidArgumentException(
					sprintf(
						'The test method %s::%s expects %d parameters, but the TestCase only has %d parameters',
						get_class($targetClass),
						$method->getName(),
						$method->getNumberOfRequiredParameters(),
						count($testCase->getParams())
					)
				);
			}
		}
		$this->target = $targetClass;
		$this->method = $method;
		$this->testCase = $testCase;
	}

	/**
	 * Checks if current TestMethod is a parameterized test
	 *
	 * @return bool
	 */
	public function isParameterizedTest() {
		return $this->testCase->hasParams();
	}

	/**
	 * Runs current test
	 *
	 * @return null|string Returns null on success or errormessage on failure
	 */
	public function run() {
		try {
			if ($this->isParameterizedTest()) {
				$this->method->invokeArgs($this->target, $this->testCase->getParams());
			} else {
				$this->method->invoke($this->target);
			}
		} catch (AssertionException $ae) {
			return $ae->getMessage();
		} catch (\Exception $e) {
			return 'Error, test failed with: ' . $e->getMessage();
		}
		return null;
	}
}