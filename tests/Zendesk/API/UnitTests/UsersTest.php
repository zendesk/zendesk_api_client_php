<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\Resources\Users;

/**
 * Users test class
 */
class UsersTest extends BasicTest
{
    protected $number;

    public function testCreate()
    {
        $testUser = [
            'id'          => '12345',
            'name'        => 'Roger Wilco',
            'email'       => 'roge@example.org',
            'role'        => 'agent',
            'verified'    => true,
            'external_id' => '3000'
        ];

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['user' => $testUser]))
        ]);

        $user = $this->client->users()->create($testUser);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'users.json',
                'postFields' => ['user' => $testUser],
            ]
        );

        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Should return a numeric id for user');
    }

    public function testDelete()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->users(12345)->delete();

        $this->assertLastRequestIs(
            [
                'method'   => 'DELETE',
                'endpoint' => 'users/12345.json',
            ]
        );
    }

    public function testAll()
    {
        $response = json_encode(
            [
                'users' => [
                    ['id' => 12345]
                ]
            ]
        );

        $this->mockAPIResponses([
            new Response(200, [], $response)
        ]);

        $users = $this->client->users()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'users.json',
            ]
        );

        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(
            is_array($users->users),
            true,
            'Should return an object containing an array called "users"'
        );

        $this->assertGreaterThan(0, $users->users[0]->id, 'Should return a numeric id for requests[0]');
    }

    public function testFind()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['user' => ['id' => 12345]]))
        ]);

        $user = $this->client->users(12345)->find();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'users/12345.json',
            ]
        );

        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Should return a numeric id for user');
    }

    public function testFindMultiple()
    {
        $findIds  = [12345, 80085];
        $response = [
            'users' => [
                ['id' => $findIds[0]],
                ['id' => $findIds[1]],
            ]
        ];

        $this->mockAPIResponses([
            new Response(200, [], json_encode($response))
        ]);

        $users = $this->client->users($findIds)->findMany();

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'users/show_many.json',
                'queryParams' => ['ids' => implode(",", [$findIds[0], $findIds[1]])],
            ]
        );
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals($users->users[0]->id, $findIds[0]);
        $this->assertEquals($users->users[1]->id, $findIds[1]);
    }

    public function testShowManyUsingIds()
    {
        $findIds  = [12345, 80085];
        $response = [
            'users' => [
                ['id' => $findIds[0]],
                ['id' => $findIds[1]],
            ]
        ];

        $this->mockAPIResponses([
            new Response(200, [], json_encode($response))
        ]);

        $users = $this->client->users()->showMany(['ids' => $findIds]);

        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(
            is_object($users->users[0]),
            true,
            'Should return an object as first "users" array element'
        );
    }

    public function testShowManyUsingExternalIds()
    {
        $findIds  = [12345, 80085];
        $response = [
            'users' => [
                ['id' => $findIds[0]],
                ['id' => $findIds[1]],
            ]
        ];

        $this->mockAPIResponses([
            new Response(200, [], json_encode($response))
        ]);

        $users = $this->client->users()->showMany(['external_ids' => $findIds]);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'users/show_many.json',
                'queryParams' => ['external_ids' => implode(',', $findIds)]
            ]
        );

        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(
            is_object($users->users[0]),
            true,
            'Should return an object as first "users" array element'
        );
    }

    public function testRelated()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['user_related' => ['requested_tickets' => 1]]))
        ]);

        $related = $this->client->users(12345)->related();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'users/12345/related.json',
            ]
        );
        $this->assertEquals(is_object($related), true, 'Should return an object');
        $this->assertEquals(
            is_object($related->user_related),
            true,
            'Should return an object called "user_related"'
        );
        $this->assertGreaterThan(
            0,
            $related->user_related->requested_tickets,
            'Should return a numeric requested_tickets for user'
        );
    }

    public function testMerge()
    {
        $postFields = ['id' => 12345];

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['user' => ['id' => 12345]]))
        ]);

        $this->client->users('me')->merge($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'users/me/merge.json',
                'postFields' => [Users::OBJ_NAME => $postFields],
            ]
        );
    }

    public function testCreateMany()
    {
        $postFields = [
            [
                'name'     => 'Roger Wilco 3',
                'email'    => 'roge3@example.org',
                'verified' => true
            ],
            [
                'name'     => 'Roger Wilco 4',
                'email'    => 'roge4@example.org',
                'verified' => true
            ]
        ];

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['job_status' => ['id' => 1]]))
        ]);

        $jobStatus = $this->client->users()->createMany($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'users/create_many.json',
                'postFields' => [Users::OBJ_NAME_PLURAL => $postFields],
            ]
        );

        $this->assertEquals(is_object($jobStatus), true, 'Should return an object');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Should return a numeric id for users[0]');
    }

    public function testUpdate()
    {
        $postFields = ['name' => 'Joe Soap'];

        $this->mockAPIResponses([
            new Response(200, [], json_encode([Users::OBJ_NAME => []]))
        ]);

        $this->client->users(12345)->update(null, $postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'users/12345.json',
                'postFields' => [Users::OBJ_NAME => $postFields],
            ]
        );
    }

    public function testUpdateMany()
    {
        $updateIds     = [12345, 80085];
        $requestParams = [
            'ids'   => $updateIds,
            'phone' => '1234567890'
        ];

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['job_status' => ['id' => 1]]))
        ]);

        $jobStatus = $this->client->users()->updateMany($requestParams);

        $this->assertLastRequestIs(
            [
                'method'      => 'PUT',
                'endpoint'    => 'users/update_many.json',
                'queryParams' => ['ids' => implode(',', $requestParams['ids'])],
                'postFields'  => [Users::OBJ_NAME => ['phone' => $requestParams['phone']]]
            ]
        );

        $this->assertEquals(is_object($jobStatus), true, 'Should return an array');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Should return a numeric id for users[0]');
    }

    public function testUpdateManyIndividualUsers()
    {
        $requestParams = [
            [
                'id'    => 12345,
                'phone' => '1234567890'
            ],
            [
                'id'    => 80085,
                'phone' => '0987654321'
            ]
        ];

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['job_status' => ['id' => 1]]))
        ]);

        $jobStatus = $this->client->users()->updateMany($requestParams);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'users/update_many.json',
                'postFields' => [Users::OBJ_NAME_PLURAL => $requestParams]
            ]
        );

        $this->assertEquals(is_object($jobStatus), true, 'Should return an array');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Should return a numeric id for users[0]');
    }

    public function testSuspend()
    {
        $userId = 12345;

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['user' => ['id' => $userId]]))
        ]);

        $user = $this->client->users($userId)->suspend();

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'users/12345.json',
                'postFields' => [Users::OBJ_NAME => ['id' => $userId, 'suspended' => true]],
            ]
        );

        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Should return a numeric id for request');
    }

    public function testSearch()
    {
        $queryParams = ['query' => 'Roger'];

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['users' => [['id' => 12345]]]))
        ]);

        $users = $this->client->users()->search($queryParams);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'users/search.json',
                'queryParams' => $queryParams,
            ]
        );

        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(
            is_array($users->users),
            true,
            'Should return an object containing an array called "users"'
        );
        $this->assertGreaterThan(0, $users->users[0]->id, 'Should return a numeric id for user');
    }

    /*
     * Needs an existed User with specified query 'name' keyword to run this function
     */
    public function testAutocomplete()
    {
        $queryParams = ['name' => 'joh'];

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['users' => [['id' => 12345]]]))
        ]);

        $users = $this->client->users()->autocomplete($queryParams);

        $this->assertLastRequestIs(
            [
                'method'      => 'POST',
                'endpoint'    => 'users/autocomplete.json',
                'queryParams' => $queryParams,
            ]
        );

        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(
            is_array($users->users),
            true,
            'Should return an object containing an array called "users"'
        );
        $this->assertGreaterThan(0, $users->users[0]->id, 'Should return a numeric id for user');
    }

    public function testUpdateProfileImageFromFile()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $id = 915987427;

        $params = [
            'file' => getcwd() . '/tests/assets/UK.png'
        ];

        $this->client->users($id)->updateProfileImageFromFile($params);

        $this->assertLastRequestIs(
            [
                'method'   => 'PUT',
                'endpoint' => "users/{$id}.json",
                'multipart'
            ]
        );
    }

    public function testUpdateProfileImageFromUrl()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $id = 915987427;

        $params = [
            'url' => 'http://www.test.com/profile.png'
        ];

        $this->client->users($id)->updateProfileImageFromUrl($params);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => "users/{$id}.json",
                'postFields' => [Users::OBJ_NAME => ['remote_photo_url' => $params['url']]],
            ]
        );
    }

    public function testAuthenticatedUser()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['user' => ['id' => 12345]]))
        ]);

        $user = $this->client->users()->me();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'users/me.json',
            ]
        );

        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Should return a numeric id for request');
    }

    public function testSetPassword()
    {
        $postFields = ['password' => 'aBc12345'];

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->users(12345)->setPassword($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'users/12345/password.json',
                'postFields' => [Users::OBJ_NAME => $postFields],
            ]
        );
    }

    public function testChangePassword()
    {
        $postFields = [
            'previous_password' => '12346',
            'password'          => '12345'
        ];

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->users(421450109)->changePassword($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'users/421450109/password.json',
                'postFields' => $postFields,
            ]
        );
    }
}
