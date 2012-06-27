<?php
use Klei\Phut\TestFixture;
use Klei\Phut\Test;
use Klei\Phut\Assert;

/**
 * @TestFixture
 */
class TheFirstTest {
    /**
     * @Test
     */
    public function MultiplyOperator_Multiply2by4_ShouldGive8()
    {
        // Given
        $number1 = 2;
        $number2 = 4;

        // When
        $result = $number1 * $number2;

        // Then
        Assert::areIdentical($result, 8);
    }
}