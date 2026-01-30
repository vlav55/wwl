<?php

namespace Pact\Tests\Service\Validation;

use Pact\Exception\InvalidArgumentException;
use Pact\Service\Validation\NumBetweenValidator;
use PHPUnit\Framework\TestCase;

class NumBetweenValidatorTest extends TestCase
{
    /** @var NumBetweenValidator */
    protected $validator;
    protected function setUp(): void
    {
        $this->validator = new NumBetweenValidator();
    }

    public function testNormalValidate()
    {
        $this->validator->validate(5, 1, 10);
        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider invalidValuesProvider
     */
    public function testNumOutsideLimitsThrowsInvalidArgument($value, $min, $max)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate($value, $min, $max);
    }

    public function invalidValuesProvider()
    {
        return [
            [0, 1, 10],
            [11, 1, 10]
        ];
    }
}
