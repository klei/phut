<?php
namespace Klei\Phut\Model;

use Klei\Phut\AssertionException;
use Klei\Phut\TestCase;
use Klei\Phut\Timer;

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
		if (!is_object($targetClass)) {
			throw new \InvalidArgumentException(sprintf('Expected parameter $targetClass to be of type object, but was: %s', gettype($targetClass)));
		}
		if (!method_exists($targetClass, $method->getName())) {
			throw new \InvalidArgumentException(sprintf('Object $targetClass does not have method "%s"', $method->getName()));
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
		return $this->testCase !== null && $this->testCase->hasParams();
	}

	/**
	 * Runs current test
	 *
	 * @return MethodResult
	 */
	public function run() {
		$timer = new Timer();
		$timer->start();
		$exception = null;
		try {
			if ($this->isParameterizedTest()) {
				$this->method->invokeArgs($this->target, $this->testCase->getParams());
			} else {
				$this->method->invoke($this->target);
			}
		} catch (\Exception $e) {
			$exception = $e;
		}
		$timer->stop();
		if ($exception !== null) {
			return new MethodResult($timer, $exception);
		}
		return new MethodResult($timer);
	}
}