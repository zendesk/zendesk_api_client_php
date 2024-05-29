<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\Client;
use InvalidArgumentException;

/**
 * InvalidSubdomain test class
 */
class InvalidSubdomainTest extends BasicTest
{
    public function testInvalidSubdomainThrows()
    {
        $this->setExpectedException('InvalidArgumentException');

        new Client('...', '');
    }

    public function testValidSubdomain()
    {
        new Client('zendesk.example.com', 'example');
        $this->assertTrue(true);
    }
}
