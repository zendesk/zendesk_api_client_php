<?php

namespace Zendesk\API\UnitTests\Core;

use Faker\Factory;
use Zendesk\API\Resources\Core\Users;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Users test class
 */
class UsersTest extends BasicTest
{
    protected $number;

    /**
     * Tests show_many using external_ids
     */
    public function testFindManyUsingExternalIds()
    {
        $findIds = [12345, 80085];

        $this->assertEndpointCalled(function () use ($findIds) {
            $this->client->users()->findMany(['external_ids' => $findIds]);
        }, 'users/show_many.json', 'GET', ['queryParams' => ['external_ids' => implode(',', $findIds)]]);
    }

    /**
     * Tests if the related enpoint can be called by the client and is passed the correct ID
     */
    public function testRelated()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->related();
        }, 'users/12345/related.json');
    }

    /**
     * Tests if the merge endpoint can be called by the client and is passed the correct data
     */
    public function testMerge()
    {
        $postFields = [
            'email' => 'thecustomer@domain.com',
            'password' => '123456',
        ];

        $this->assertEndpointCalled(function () use ($postFields) {
            $this->client->users()->merge($postFields);
        }, 'users/me/merge.json', 'PUT', ['postFields' => ['user' => $postFields]]);
    }

    /**
     * Tests if the merge endpoint can be called with admin params and is passed the correct data
     */
    public function testAdminMerge()
    {
        $userId = 12345;
        $mergingId = 123456;

        $postFields = [
            'id' => $mergingId,
        ];

        $this->assertEndpointCalled(function () use ($userId, $postFields) {
            $this->client->users($userId)->merge($postFields);
        }, "users/{$userId}/merge.json", 'PUT', ['postFields' => ['user' => $postFields]]);
    }


    /**
     * Tests if the suspend enpoint can be called by the client and is passed the correct ID
     */
    public function testSuspend()
    {
        $userId = 12345;

        $this->assertEndpointCalled(
            function () use ($userId) {
                $this->client->users($userId)->suspend();
            },
            'users/12345.json',
            'PUT',
            [
                'postFields' => ['user' => ['id' => $userId, 'suspended' => true]]
            ]
        );
    }

    /**
     * Tests if the search enpoint can be called by the client and is passed the correct query
     */
    public function testSearchByQuery()
    {
        $queryParams = ['query' => 'Roger'];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->users()->search($queryParams);
        }, 'users/search.json', 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Tests if the search enpoint can be called by the client and is passed the correct external_id
     */
    public function testSearchByExternalId()
    {
        $queryParams = ['external_id' => 'ext-1'];
        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->users()->search($queryParams);
        }, 'users/search.json', 'GET', ['queryParams' => $queryParams]);
    }

    /*
     * Needs an existed User with specified query 'name' keyword to run this function
     */
    public function testAutocomplete()
    {
        $queryParams = ['name' => 'joh'];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->users()->autocomplete($queryParams);
        }, 'users/autocomplete.json', 'POST', ['queryParams' => $queryParams]);
    }

    /**
     * Tests if the client can perform the update profile functionality
     */
    public function testUpdateProfileImageFromFile()
    {
        $id = 915987427;

        $this->assertEndpointCalled(function () use ($id) {
            $params = [
                'file' => getcwd() . '/tests/assets/UK.png'
            ];
            $this->client->users($id)->updateProfileImageFromFile($params);
        }, "users/{$id}.json", 'PUT', ['multipart' => true]);
    }

    /**
     * Tests if the client can perform the update profile image functionality
     */
    public function testUpdateProfileImageFromUrl()
    {
        $id = 915987427;

        $params = [
            'url' => 'http://www.test.com/profile.png'
        ];

        $this->assertEndpointCalled(function () use ($id, $params) {
            $this->client->users($id)->updateProfileImageFromUrl($params);
        }, "users/{$id}.json", 'PUT', ['postFields' => ['user' => ['remote_photo_url' => $params['url']]]]);
    }

    /**
     * Tests if the client can call the users/me.json endpoint
     */
    public function testAuthenticatedUser()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users()->me();
        }, 'users/me.json');
    }

    /*
     * Tests if the setPassword function calls the correct endpoint and passes the correct POST data
     */
    public function testSetPassword()
    {
        $postFields = ['password' => 'aBc12345'];

        $this->assertEndpointCalled(function () use ($postFields) {
            $this->client->users(12345)->setPassword($postFields);
        }, 'users/12345/password.json', 'POST', ['postFields' => $postFields]);
    }

    /**
     * Tests if the changePassword function calls the correct endpoint and passes the correct PUT data
     */
    public function testChangePassword()
    {
        $postFields = [
            'previous_password' => '12346',
            'password'          => '12345'
        ];

        $userId = 421450109;

        $this->assertEndpointCalled(function () use ($postFields, $userId) {
            $this->client->users($userId)->changePassword($postFields);
        }, "users/{$userId}/password.json", 'PUT');
    }

    /*
     * Tests if the createOrUpdate function calls the correct endpoint and passes the correct POST data
     */
    public function testCreateOrUpdate()
    {
        $faker = Factory::create();
        $postFields = ['id' => $faker->uuid, 'name' => $faker->name];
        $result = ['user' => $postFields];
        $this->assertEndpointCalled(function () use ($postFields) {
            $this->client->users()->createOrUpdate($postFields);
        }, 'users/create_or_update.json', 'POST', ['postFields' => $result]);
    }
}
