<?php
namespace Klei\Phut\Model;

use Klei\Phut\MethodHandler;

class TestContainer {
	/**
	 * @var MethodHandler
	 */
	protected $methodHandler;

	/**
	 * @var array<\ReflectionMethod>
	 */
	protected $methods;

	/**
	 * @var SetupMethod
	 */
	protected $setup;

	/**
	 * @var TeardownMethod
	 */
	protected $teardown;

	/**
	 * @var array<TestMethod>
	 */
	protected $tests;

	/**
	 * @var object The current TestFixture class
	 */
	protected $target;

	/**
	 * @var string The fully qualified class name for the TestFixture class
	 */
	protected $targetClassName;

	/**
	 * @param string $testFixtureClassName The fully qualified class name for the TestFixture class
	 * @return void
	 */
	public function __construct($testFixtureClassName) {
		if (!class_exists($testFixtureClassName, false)) {
			throw new \InvalidArgumentException(sprintf('The specified class does not exist: "%s". Could not create %s.', $testFixtureClassName, __CLASS__));
		}
		$this->targetClassName = $testFixtureClassName;
	}

	public function init() {
		$this->instantiateTarget();
		$this->extractRelevantMethodsFromTarget();
	}

	public function setMethodHandler(MethodHandler $methodHandler) {
        $this->methodHandler = $methodHandler;
    }

    public function getMethodHandler() {
        if ($this->methodHandler == null) {
            $this->methodHandler = new MethodHandler();
        }
        return $this->methodHandler;
    }

	protected function instantiateTarget() {
		$class = $this->targetClassName;
		$this->target = new $class;
	}

	protected function extractRelevantMethodsFromTarget() {
		$handler = $this->getMethodHandler();
		$methods = $handler->getMethods($this->target);
		$this->setup = $handler->extractSetupMethod($methods);
		$this->tests = $handler->extractTestMethods($methods);
		$this->teardown = $handler->extractTeardownMethod($methods);
	}

	/**
	 * Checks if current TestContainer has a setup method
	 *
	 * @return bool
	 */
	public function hasSetup() {
		return $this->setup !== null;
	}

	/**
	 * @return MethodResult
	 */
	public function runSetup() {
		return $this->setup->run();
	}

	/**
	 * Checks if current TestContainer has a teardown method
	 *
	 * @return bool
	 */
	public function hasTeardown() {
		return $this->teardown !== null;
	}

	/**
	 * @return MethodResult
	 */
	public function runTeardown() {
		return $this->teardown->run();
	}

	/**
	 * @return array<MethodResult>
	 */
	public function runTests() {
		$results = array();
		foreach ($this->tests as $test) {
			$results[] = $test->run();
		}
		return $results;
	}

	/**
	 * @return array<MethodResult>
	 */
	public function run() {
		$results = array();
		$runTests = true;

		if ($this->hasSetup()) {
			$setupResult = $this->runSetup();
			$runTests = $setupResult->isSuccessful();
			$results[] = $setupResult;
		}

		if ($runTests) {
			$results = array_merge($results, $this->runTests());
		}

		if ($this->hasTeardown()) {
			$results[] = $this->runTeardown();
		}

		return $results;
	}
}