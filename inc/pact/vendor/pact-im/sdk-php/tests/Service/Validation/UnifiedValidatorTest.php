<?php

namespace Pact\Tests\Service\Validation;

use Pact\Exception\InvalidArgumentException;
use Pact\Service\Validation\UnifiedValidator;
use PHPUnit\Framework\TestCase;

class UnifiedValidatorTest extends TestCase
{
    /** @var UnifiedValidator */
    protected $validator;
    protected function setUp(): void
    {
        $this->validator = new UnifiedValidator();
    }

    public function testNormalValidate()
    {
        $this->validator->validate(false, 'This should not throw exception');
        $this->addToAssertionCount(1);
    }

    public function testInvalidSortThrowsInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate(true, 'Invalid argument exception must be thrown');
    }
}
