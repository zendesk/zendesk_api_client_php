<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

class CustomRolesTest extends BasicTest
{
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->customRoles(), 'findAll'));
    }
}
