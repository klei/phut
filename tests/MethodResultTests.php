<?php
use Klei\Phut\Test;
use Klei\Phut\TestFixture;
use Klei\Phut\Assert;
use Klei\Phut\Timer;
use Klei\Phut\AssertionException;
use Klei\Phut\Model\MethodResult;

/**
 * @TestFixture
 */
class MethodResultTests {
    const NONSENSE = 'YADA';
    /**
      * @Test
      */
    public function newMethodResult_ValidTimerNoException_DoesNotThrow() {
        // Given
        $timer = new Timer();
        $timer->start();
        $timer->stop();
        $name = self::NONSENSE;

        // When
        $toTest = function() use($timer, $name) {
            new MethodResult($name, $timer);
        };

        // Then
        Assert::doesNotThrow($toTest);
    }

    /**
      * @Test
      */
    public function newMethodResult_InvalidTimerNoException_ThrowsInvalidArgumentException() {
        // Given
        $timer = new Timer();
        $name = self::NONSENSE;

        // When
        $toTest = function() use($timer, $name) {
            new MethodResult($name, $timer);
        };

        // Then
        Assert::throws('InvalidArgumentException', $toTest);
    }

    /**
      * @Test
      */
    public function newMethodResult_ValidTimerWithException_DoesNotThrow() {
        // Given
        $timer = new Timer();
        $timer->start();
        $timer->stop();
        $exception = new \Exception(self::NONSENSE);
        $name = self::NONSENSE;

        // When
        $toTest = function() use($timer, $exception, $name) {
            new MethodResult($name, $timer, $exception);
        };

        // Then
        Assert::doesNotThrow($toTest);
    }

    /**
      * @Test
      */
    public function newMethodResult_WithException_IsNotSuccessful() {
        // Given
        $timer = new Timer();
        $timer->start();
        $timer->stop();
        $exception = new \Exception(self::NONSENSE);
        $methodResult = new MethodResult(self::NONSENSE, $timer, $exception);

        // Then
        Assert::isFalse($methodResult->isSuccessful());
    }

    /**
      * @Test
      */
    public function newMethodResult_WithoutException_IsSuccessful() {
        // Given
        $timer = new Timer();
        $timer->start();
        $timer->stop();
        $methodResult = new MethodResult(self::NONSENSE, $timer);

        // Then
        Assert::isTrue($methodResult->isSuccessful());
    }

    /**
      * @Test
      */
    public function newMethodResult_WithAssertionException_IsNotUnexpectedException() {
        // Given
        $timer = new Timer();
        $timer->start();
        $timer->stop();
        $exception = new AssertionException(self::NONSENSE);
        $methodResult = new MethodResult(self::NONSENSE, $timer, $exception);

        // Then
        Assert::isFalse($methodResult->isUnexpectedException());
    }

    /**
      * @Test
      */
    public function newMethodResult_WithNoAssertionException_IsUnexpectedException() {
        // Given
        $timer = new Timer();
        $timer->start();
        $timer->stop();
        $exception = new \Exception(self::NONSENSE);
        $methodResult = new MethodResult(self::NONSENSE, $timer, $exception);

        // Then
        Assert::isTrue($methodResult->isUnexpectedException());
    }
}