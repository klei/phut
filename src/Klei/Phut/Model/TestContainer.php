<?php
namespace Klei\Phut\Model;

class TestContainer {
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

	}

	public function instantiateTarget() {
		$class = $this->targetClassName;
		$this->target = new $class;
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