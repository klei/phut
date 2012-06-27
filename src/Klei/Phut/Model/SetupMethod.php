<?php
namespace Klei\Phut\Model;

use Klei\Phut\AssertionException;

class SetupMethod implements IRunnable {
	/**
	 * @var object
	 */
	protected $target;

	/**
	 * @var \ReflectionMethod
	 */
	protected $method;

	/**
	 * @param object $targetClass
	 * @param \ReflectionMethod $method
	 */
	public function __construct($targetClass, \ReflectionMethod $method) {
		if (!method_exists($targetClass, $method->getName())) {
			throw new \InvalidArgumentException(sprintf('Expected parameter $targetClass to have the method "%s", but it was not found on object of type: %s', $method->getName(), gettype($targetClass)));
		}
		if ($method->getNumberOfRequiredParameters() > 0) {
			throw new \InvalidArgumentException(sprintf('Expected setup method %s::%s to take no parameters, but it requires %d parameters', getclass($targetClass), $method->getName(), $method->getNumberOfRequiredParameters()));
		}
		$this->target = $targetClass;
		$this->method = $method;
	}

	/**
	 * Runs the current setup method
	 *
	 * @return null|string Returns null on success or errormessage on failure
	 */
	public function run() {
		try {
			$this->method->invoke($this->target);
		} catch (AssertionException $ae) {
			return $ae->getMessage();
		} catch (\Exception $e) {
			return 'Error, TestFixture Setup failed with: ' . $e->getMessage();
		}
		return null;
	}
}