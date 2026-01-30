<?php

namespace Pact\Tests\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;
use Pact\Http\Factory;

class FactoryTest extends TestCase
{
    /**
     * Test that we can create curl client successfully
     * with implemented interface
     */
    public function testClientCreationSuccessful()
    {
        $client = Factory::client();
        $this->assertNotEmpty($client);
        $this->assertInstanceOf(ClientInterface::class, $client);
    }

    /**
     * Test that we can create request object successfully
     * with implemented interface
     */
    public function testRequestCreationSuccessful()
    {
        $request = Factory::request('GET', '');
        $this->assertNotEmpty($request);
        $this->assertInstanceOf(RequestInterface::class, $request);    
    }

    /**
     * Test that we can create request object successfully
     * with implemented interface
     */
    public function testResponseCreationSuccessful()
    {
        $response = Factory::response();
        $this->assertNotEmpty($response);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
