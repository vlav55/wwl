<?php

namespace Pact\Tests\Service\Validation;

use Pact\Exception\InvalidArgumentException;
use Pact\Service\Validation\SortValidatior;
use PHPUnit\Framework\TestCase;

class SortValidatorTest extends TestCase
{
    /** @var SortValidatior */
    protected $validator;
    protected function setUp(): void
    {
        $this->validator = new SortValidatior();
    }

    public function testNormalValidate()
    {
        $this->validator->validate('asc');
        $this->addToAssertionCount(1);
        $this->validator->validate('desc');
        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider invalidValuesProvider
     */
    public function testInvalidSortThrowsInvalidArgument($sort)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate($sort);
    }

    public function invalidValuesProvider()
    {
        return [
            ['asdf'],
            ['ask'],
            ['desv']
        ];
    }
}
