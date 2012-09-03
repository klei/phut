<?php
namespace Klei\Phut\Model;

use ReflectionMethod;
use Klei\Phut\MethodHandler;

class TestContainer {
    /**
     * @var MethodHandler
     */
    protected $methodHandler;

    /**
     * @var array<ReflectionMethod>
     */
    protected $methods;

    /**
     * @var array<MethodResult>
     */
    protected $methodResults;

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
        $this->setTargetClassName($testFixtureClassName);
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

    public function resetMethodResults()
    {
        $this->methodResults = array();
    }

    public function getMethodResults()
    {
        return $this->methodResults;
    }

    /**
     * @param MethodResult $methodResult The MethodResult to add to the collection
     * @return MethodResult The added MethodResult
     */
    public function appendMethodResult(MethodResult $methodResult)
    {
        $this->methodResults[] = $methodResult;
        return $methodResult;
    }

    public function getName()
    {
        return $this->targetClassName;
    }

    public function setTargetClassName($targetClassName)
    {
        if (!class_exists($targetClassName, false))
            throw new \InvalidArgumentException(sprintf('The specified class does not exist: "%s". Could not create %s.', $targetClassName, __CLASS__));
        $this->targetClassName = $targetClassName;
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
     * Checks if current TestContainer has a teardown method
     *
     * @return bool
     */
    public function hasTeardown() {
        return $this->teardown !== null;
    }

    /**
     * @return bool True if successful
     */
    public function runSetup() {
        if (!$this->hasSetup())
            return true;

        $setupResult = $this->appendMethodResult($this->setup->run());

        return $setupResult->isSuccessful();
    }

    /**
     * @param array<MethodResult> $methodResults
     * @return void
     */
    public function runTeardown() {
        if (!$this->hasTeardown)
            return
        $this->appendMethodResult($this->teardown->run());
    }

    /**
     * @return array<MethodResult>
     */
    public function runTests() {
        foreach ($this->tests as $test) {
            $this->appendMethodResult($test->run());
        }
    }

    /**
     * @return bool True on success
     */
    public function run() {
        $this->resetMethodResults();

        $doRunTests = $this->runSetup();

        if ($doRunTests)
            $this->runTests();

        $this->runTeardown();

        return $this->isSuccessful();
    }

    public function getFailedMethodResults()
    {
        return array_filter($this->methodResults, function ($methodResult) {
            return !$methodResult->isSuccessful();
        });
    }

    public function isSuccessful()
    {
        return (bool)(count($this->getFailedMethodResults()) === 0);
    }
}
