<?php

namespace Pact\Tests;

use Pact\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Pact\PactClientBase;
use Pact\Service\ServiceInterface;

class PactClientBaseTest extends TestCase
{
    public function testTokenlessAppThrowInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('API token can\'t be empty string');

        new PactClientBase('');
    }
}
