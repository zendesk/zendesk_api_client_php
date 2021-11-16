<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\Client;
use InvalidArgumentException;

/**
 * InvalidSubdomain test class
 */
class InvalidSubdomainTest extends BasicTest
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidSubdomainThrows()
    {
        new Client('...', '');
    }
}
