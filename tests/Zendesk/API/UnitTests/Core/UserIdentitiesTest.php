<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\Resources\Core\UserIdentities;
use Zendesk\API\UnitTests\BasicTest;

/**
 * UserIdentities test class
 */
class UserIdentitiesTest extends BasicTest
{

    /**
     * Tests if the unique routes are called correctly
     */
    public function testRoutes()
    {
        $userId = 3124;
        $id     = 123;

        // FindAll
        $this->assertEquals(
            "users/{$userId}/identities.json",
            $this->client->users($userId)->identities()->getRoute(
                'findAll',
                ['userId' => $userId]
            )
        );

        // Create
        $this->assertEquals(
            "users/{$userId}/identities.json",
            $this->client->users($userId)->identities()->getRoute(
                'create',
                ['userId' => $userId]
            )
        );

        // Find
        $this->assertEquals(
            "users/{$userId}/identities/{$id}.json",
            $this->client->users($userId)->identities($id)->getRoute(
                'find',
                ['id' => $id, 'userId' => $userId]
            )
        );

        // Delete
        $this->assertEquals(
            "users/{$userId}/identities/{$id}.json",
            $this->client->users($userId)->identities($id)->getRoute(
                'delete',
                ['id' => $id, 'userId' => $userId]
            )
        );

        // Update
        $this->assertEquals(
            "users/{$userId}/identities/{$id}.json",
            $this->client->users($userId)->identities($id)->getRoute(
                'update',
                ['id' => $id, 'userId' => $userId]
            )
        );
    }

    /**
     * Tests if the client can POST to the identities end point
     */
    public function testCreateAsEndUser()
    {
        $userId = 3124;

        $postFields = [
            'type'  => 'email',
            'value' => 'thecustomer@domain.com'
        ];

        $this->assertEndpointCalled(function () use ($userId, $postFields) {
            $this->client->users($userId)->identities()->createAsEndUser($postFields);
        }, "end_users/{$userId}/identities.json", 'POST', ['postFields' => ['identity' => $postFields]]);
    }

    /**
     * Tests if the client can call and build the verify endpoint
     */
    public function testVerify()
    {
        $userId = 3124;
        $id     = 123;

        $this->assertEndpointCalled(function () use ($userId, $id) {
            $this->client->users($userId)->identities($id)->verify();
        }, "users/{$userId}/identities/{$id}/verify.json", 'PUT');
    }

    /**
     * Tests if the client can call and build the make primary endpoint
     */
    public function testMakePrimary()
    {
        $userId = 3124;
        $id     = 123;

        $this->assertEndpointCalled(function () use ($id, $userId) {
            $this->client->users($userId)->identities($id)->makePrimary();
        }, "users/$userId/identities/$id/make_primary.json", 'PUT');
    }

    /**
     * Tests if the client can call and build the request verification endpoint
     */
    public function testRequestVerification()
    {
        $userId = 3124;
        $id     = 123;

        $this->assertEndpointCalled(function () use ($id, $userId) {
            $this->client->users($userId)->identities($id)->requestVerification();
        }, "users/{$userId}/identities/{$id}/request_verification.json", 'PUT');
    }
}
