<?php
use Klei\Phut\Setup;
use Klei\Phut\Teardown;
use Klei\Phut\Test;
use Klei\Phut\TestFixture;
use Klei\Phut\Assert;
use Klei\Phut\Model\TestContainer;

/**
 * @TestFixture
 */
class TestContainerTests {
    /**
      * @Test
      */
    public function hasSetup_TestFixtureWithSetupMethod_EqualsTrue() {
        // Given
        $testContainer = new TestContainer('TheTestClass');
        $testContainer->init();

        // Then
        Assert::isTrue($testContainer->hasSetup());
    }

    /**
      * @Test
      */
    public function hasTeardown_TestFixtureWithTeardownMethod_EqualsTrue() {
        // Given
        $testContainer = new TestContainer('TheTestClass');
        $testContainer->init();

        // Then
        Assert::isTrue($testContainer->hasTeardown());
    }
}

class TheTestClass {
    /**
     * @Setup
     */
    public function Setup() {
        
    }

    /**
     * @Test
     */
    public function Test() {
        
    }

    /**
     * @Teardown
     */
    public function Teardown() {
        
    }

    public function NoAnnotation() {
        
    }
}