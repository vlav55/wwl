<?php

namespace Pact\Tests\Service;

use Pact\Http\Factory;
use Pact\PactClient;
use Pact\PactClientInterface;
use Pact\Service\AbstractService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ServiceTestCase extends TestCase
{
    protected static $serviceClass;

    /** @var AbstractService */
    protected $service;

    /** @var PactClientInterface|MockObject */
    protected $client;

    /** @var string $expectedMethod */
    protected $expectedMethod;

    /** @var string $expectedUrl endpoint */
    protected $expectedUrl;

    /** @var ResponseInterface */
    protected $serviceResponse = null;

    protected function mockServiceResponse()
    {
        if ($this->serviceResponse === null) {
            $this->serviceResponse = Factory::response(200, [], '{"status":"ok"}');
        }
        return $this->serviceResponse;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->expectedMethod = '';
        $this->expectedUrl = '';
        $this->setUpMocks();
    }

    protected function setUpMocks($body = null, $headers = null, $method = null, $url = null) 
    {
        if ($body === null) {
            $body = $this->anything();
        }
        if ($headers === null) {
            $headers = $this->anything();
        }
        if ($url === null) {
            $url = $this->callback(function($url) {
                $this->assertSame($this->expectedUrl, $url);
                return true;
            });
        }
        if ($method === null) {
            $method = $this->callback(function($method) {
                $this->assertSame($this->expectedMethod, $method);
                return true;
            });
        }

        /** @var PactClientInterface|MockObject */
        $this->client = $this->getMockBuilder(PactClient::class)
            ->setConstructorArgs(['top-secret token do not look 0w0'])
            ->getMock();

            
        $this->client->expects($this->any())
            ->method('request')
            ->with($method, $url, $headers, $body)
            ->will($this->returnValue($this->mockServiceResponse()));
        
        $this->service = new static::$serviceClass($this->client);
    }

    protected function formatEndpoint($append = '', array $routeParams = [], array $query = [])
    {
        $template = $this->service::SERVICE_ENDPOINT;
        return $this->service->formatEndpoint($template.$append, $routeParams, $query);
    }
}
