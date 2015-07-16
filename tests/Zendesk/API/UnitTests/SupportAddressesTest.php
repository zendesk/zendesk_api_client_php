<?php

namespace Zendesk\API\UnitTests;

class SupportAddressesTest extends BasicTest
{
    /**
     * Tests if the client can build the verify support address endpoint and pass the update fields
     */
    public function testVerify()
    {
        $updateData = ['type' => 'forwarding'];

        $this->assertEndpointCalled(function () use ($updateData) {
            $this->client->supportAddresses()->verify(123, $updateData);
        }, 'recipient_addresses/123/verify.json', 'PUT', ['postFields' => $updateData]);
    }
}
