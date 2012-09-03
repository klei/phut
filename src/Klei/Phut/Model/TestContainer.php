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
            $this->setMethodHandler(new MethodHandler());
        }
        return $this->methodHandler;
    }

    public function getName()
    {
        return $this->targetClassName;
    }

    protected function instantiateTarget() {
        $class = $this->targetClassName;
        $this->target = new $class;
    }

    protected function extractRelevantMethodsFromTarget() {
        $handler = $this->getMethodHandler();
        $methods = $this->getMethodsFromTarget($handler);
        $this->extractSetupMethod($handler, $methods);
        $this->extractTestMethods($handler, $methods);
        $this->extractTeardownMethod($handler, $methods);
    }

    protected function getMethodsFromTarget(MethodHandler $handler)
    {
        return $handler->getMethods($this->target);
    }

    protected function extractSetupMethod(MethodHandler $handler, array $methods)
    {
        $this->setup = $handler->extractSetupMethod($methods);
    }

    protected function extractTestMethods(MethodHandler $handler, array $methods)
    {
        $this->tests = $handler->extractTestMethods($methods);
    }

    protected function extractTeardownMethod(MethodHandler $handler, array $methods)
    {
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
     * @param array<MethodResult> $methodResults
     * @return bool True if successful
     */
    public function runSetupAndGatherResult(array &$methodResults) {
        if (!$this->hasSetup())
            return true;

        $setupResult = $this->setup->run();

        $this->gatherSetupResultOnSuccess($setupResult, $methodResults);

        return $setupResult->isSuccessful();
    }

    protected function gatherSetupResultOnSuccess(MethodResult $setupResult, array &$methodResults)
    {
        if ($setupResult->isSuccessful())
            $methodResults += $setupResult;
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
     * @param array<MethodResult> $methodResults
     * @return void
     */
    public function runTeardownAndGatherResult(array &$methodResults) {
        if (!$this->hasTeardown)
            return
        $methodResults += $this->teardown->run();
    }

    /**
     * @return array<MethodResult>
     */
    public function runTestsAndGatherResult(array &$methodResults) {
        foreach ($this->tests as $test) {
            $methodResults += $test->run();
        }
    }

    /**
     * @return array<MethodResult>
     */
    public function run() {
        $results = array();

        $doRunTests = $this->runSetupAndGatherResult($results);

        if ($doRunTests)
            $this->runTestsAndGatherResult($results);

        $this->runTeardownAndGatherResult($results);

        return $results;
    }
}
