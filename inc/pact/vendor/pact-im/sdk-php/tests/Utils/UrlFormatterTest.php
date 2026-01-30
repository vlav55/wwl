<?php

namespace Pact\Tests\Service;

use Pact\Exception\InvalidArgumentException;
use Pact\Utils\UrlFormatter;
use PHPUnit\Framework\TestCase;

class UrlFormatterTest extends TestCase
{
    public function testFormatWithInvalidParametersThrowsInvalidArgumentException()
    {
        $this->expectExceptionMessage('The resource ID cannot be null or whitespace.');
        $this->expectException(InvalidArgumentException::class);
        UrlFormatter::format('/test/%s/path/%s/', ['', 'df']);
    }

    public function testValidParametersWithOK()
    {
        $this->assertSame('/5/test/df/', UrlFormatter::format('/%s/test/%s/', [5, 'df']));
        $this->assertSame('/5/test/df/?test=1', UrlFormatter::format('/%s/test/%s/', [5, 'df'], ["test"=>1]));
        $this->assertSame('/%24%25tst/path/', UrlFormatter::format('/%s/path/', ['$%tst']));
    }
}
