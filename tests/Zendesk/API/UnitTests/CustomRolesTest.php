<?php

namespace Zendesk\API\UnitTests;

class CustomRolesTest extends BasicTest
{
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->customRoles(), 'findAll'));
    }
}
