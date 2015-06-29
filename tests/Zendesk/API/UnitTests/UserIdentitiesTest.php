<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\Resources\UserIdentities;

/**
 * UserIdentities test class
 */
class UserIdentitiesTest extends BasicTest
{

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

    public function testCreateAsEndUser()
    {

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $userId = 3124;

        $postFields = [
            'type'  => 'email',
            'value' => 'devaris.brown@zendesk.com'
        ];

        $this->client->users($userId)->identities()->createAsEndUser($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => "end_users/{$userId}/identities.json",
                'postFields' => [UserIdentities::OBJ_NAME => $postFields]
            ]
        );
    }

    public function testVerify()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $userId = 3124;
        $id     = 123;

        $this->client->users($userId)->identities($id)->verify();

        $this->assertLastRequestIs(
            [
                'method'   => 'PUT',
                'endpoint' => "users/{$userId}/identities/{$id}/verify.json",
            ]
        );
    }

    public function testMakePrimary()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $userId = 3124;
        $id     = 123;

        $this->client->users($userId)->identities($id)->makePrimary();

        $this->assertLastRequestIs(
            [
                'method'   => 'PUT',
                'endpoint' => "users/$userId/identities/$id/make_primary.json",
            ]
        );
    }

    public function testRequestVerification()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $userId = 3124;
        $id     = 123;

        $this->client->users($userId)->identities($id)->requestVerification();

        $this->assertLastRequestIs(
            [
                'method'   => 'PUT',
                'endpoint' => "users/{$userId}/identities/{$id}/request_verification.json",
            ]
        );
    }
}
