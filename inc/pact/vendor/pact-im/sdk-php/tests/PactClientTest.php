<?php

namespace Pact\Tests;

use Pact\PactClient;
use Pact\Service\ServiceInterface;
use PHPUnit\Framework\TestCase;

class PactClientTest extends TestCase
{
    public function testGetExistingServiceShouldBeOk()
    {
        $client = new PactClient('super secret, do not look 0w0');

        $service = $client->messages;
        $this->assertNotEmpty($service);
        $this->assertInstanceOf(ServiceInterface::class, $service);

        $service2 = $client->messages;
        $this->assertNotEmpty($service);
        $this->assertSame($service, $service2);
    }
}
