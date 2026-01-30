<?php

namespace Pact\Tests\Service;

use Pact\Exception\ApiCallException;
use Pact\Http\Factory;
use Pact\Http\Methods;
use Pact\PactClientBase;
use Pact\PactClientInterface;
use Pact\Service\AbstractService;
use PHPUnit\Framework\TestCase;

class AbstractServiceTest extends TestCase
{
    private $client = null;

    /** @var AbstractService */
    private $abstractService = null;

    protected function setUp(): void
    {
        /** @var PactClientInterface */
        $this->client = $this->getMockBuilder(PactClientBase::class)
            ->setConstructorArgs(['top-secret token do not look 0w0'])
            ->getMock();

        $this->abstractService = $this->getMockForAbstractClass(AbstractService::class, [$this->client]);
    }

    public function test_valid_get_route_returns_empty_string()
    {
        $this->assertSame('', $this->abstractService->formatEndpoint('', [], []));
    }

    public function test_valid_get_route_with_query()
    {
        $this->assertSame('?test=1', $this->abstractService->formatEndpoint('', [], ['test'=>'1']));
    }

    public function test_valid_make_request()
    {
        $this->client->expects($this->any())
            ->method('request')
            ->will($this->returnValue(Factory::response(200, [], '{"status":"ok"}')));
        $this->abstractService->request(Methods::GET, '', [], ['test'=>1]);
        $this->addToAssertionCount(1);
    }

    public function test_non_ok_status_throws_exception()
    {
        $this->expectException(ApiCallException::class);
        $this->expectExceptionMessageMatches('/^Api returned HTTP non-OK status: .+/');
        $this->client->expects($this->any())
            ->method('request')
            ->will($this->returnValue(Factory::response(404, [], '{}')));
        $this->abstractService->request(Methods::GET, '', [], []);
        $this->addToAssertionCount(1);
    }
}
