<?php
namespace Klei\Phut\Model;

use Klei\Phut\MethodHandler;

class TestContainer {
	/**
	 * @var MethodHandler
	 */
	protected $methodHandler;

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
	 * @return void
	 */
	public function runSetup() {
		$this->setup->run();
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
	 * @return void
	 */
	public function runTeardown() {
		$this->teardown->run();
	}
}