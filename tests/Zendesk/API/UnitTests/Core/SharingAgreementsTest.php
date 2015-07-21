<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Sharing Agreements test class
 */
class SharingAgreementsTest extends BasicTest
{

    /**
     * Test that the findAll method was included
     */
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->sharingAgreements(), 'findAll'));
    }
}
