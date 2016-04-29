<?php
namespace Zendesk\API\LiveTests;

use Faker\Factory;
use Zendesk\API\Exceptions\ApiResponseException;

/**
 * Users test class
 */
class UsersTest extends BasicTest
{
    /**
     * Tests creating a user
     */
    public function testCreate()
    {
        $faker      = Factory::create();
        $userFields = [
            'name'        => $faker->name,
            'email'       => $faker->safeEmail,
            'verified'    => true,
            'external_id' => $faker->uuid,
        ];
        $response   = $this->client->users()->create($userFields);
        $this->assertTrue(property_exists($response, 'user'));
        $this->assertEquals($userFields['name'], $response->user->name);
        $this->assertEquals(strtolower($userFields['email']), $response->user->email);
        $this->assertEquals($userFields['verified'], $response->user->verified);

        return $response->user;
    }

    /**
     * Tests listing of users
     *
     * @depends testCreate
     */
    public function testFindAll($user)
    {
        $response = $this->client->users()->findAll();
        $this->assertTrue(property_exists($response, 'users'));
        $this->assertGreaterThan(0, count($response->users));
    }

    /**
     * Tests find a single user
     *
     * @depends testCreate
     */
    public function testFind($user)
    {
        $response = $this->client->users($user->id)->find();
        $this->assertTrue(property_exists($response, 'user'));
        $this->assertEquals($user->id, $response->user->id);
    }

    /**
     * Tests search for a user
     *
     * @depends testCreate
     */
    public function testSearch($user)
    {
        $response = $this->client->users()->search(['query' => $user->name]);
        $this->assertTrue(property_exists($response, 'users'));
        $this->assertNotNull($foundUser = $response->users[0]);
        $this->assertEquals($user->email, $foundUser->email);
        $this->assertEquals($user->name, $foundUser->name);
    }

    /**
     * Tests search for a user
     *
     * @depends testCreate
     */
    public function testSearchByExternalId($user)
    {
        $response = $this->client->users()->search(['external_id' => $user->external_id]);
        $this->assertTrue(property_exists($response, 'users'));
        $this->assertNotNull($foundUser = $response->users[0]);
        $this->assertEquals($user->email, $foundUser->email);
        $this->assertEquals($user->name, $foundUser->name);
    }

    /**
     * Tests find a single user
     *
     * @depends testCreate
     */
    public function testGetRelatedInformation($user)
    {
        $response = $this->client->users($user->id)->related();
        $this->assertTrue(property_exists($response, 'user_related'));
    }

    /**
     * Tests update
     *
     * @depends testCreate
     */
    public function testUpdate($user)
    {
        $faker      = Factory::create();
        $userFields = [
            'name' => $faker->name,
        ];

        $response = $this->client->users()->update($user->id, $userFields);

        $this->assertEquals($userFields['name'], $response->user->name);
    }

    /**
     * Tests create or update users
     *
     */
    public function testCreateOrUpdate()
    {
        $faker      = Factory::create();
        $userFields = [
            'email' => $faker->email,
            'name'  => $faker->name,
        ];

        $response = $this->client->users()->createOrUpdate($userFields);
        $this->assertEquals($userFields['name'], $response->user->name);
    }

    /**
     * Tests create or update many users
     *
     */
    public function testCreateOrUpdateMany()
    {
        $faker      = Factory::create();
        $userFields = [
            [
                'email' => $faker->email,
                'name'  => $faker->name,
            ]
        ];

        $response = $this->client->users()->createOrUpdateMany($userFields);

        // Test the job was queued
        $this->assertEquals('queued', $response->job_status->status);
    }

    /**
     * Tests if the client can upload a file to update the profile photo
     *
     * @depends testCreate
     */
    public function testUpdateProfileImageFromFile($user)
    {
        $params   = [
            'file' => getcwd() . '/tests/assets/UK.png'
        ];
        $response = $this->client->users($user->id)->updateProfileImageFromFile($params);
        $this->assertTrue(property_exists($response->user, 'photo'));
        $this->assertEquals('UK.png', $response->user->photo->file_name);
    }

    /**
     * Tests if the client can call the users/me.json endpoint
     */
    public function testAuthenticatedUser()
    {
        $response = $this->client->users()->me();
        $this->assertTrue(property_exists($response, 'user'));
        $this->assertEquals($this->username, $response->user->email);
    }

    /**
     * Tests if the setPassword function calls the correct endpoint and passes the correct POST data
     *
     * @depends testCreate
     */
    public function testSetPassword($user)
    {
        $postFields = ['password' => 'aBc12345'];

        try {
            $this->client->users($user->id)->setPassword($postFields);
            $this->assertEquals(200, $this->client->getDebug()->lastResponseCode);
            $this->assertNull($this->client->getDebug()->lastResponseError);
        } catch (ApiResponseException $e) {
            if ($e->getCode() === 403) {
                $this->markTestSkipped('Skipping test, `Allow admins to set passwords` must be enabled in Security.');
            }
        }

        return $user;
    }

    /**
     * Tests if the changePassword function calls the correct endpoint and passes the correct PUT data
     *
     * @depends testSetPassword
     */
    public function testChangePassword($user)
    {
        $this->client->setAuth('basic', ['username' => $user->email, 'token' => $this->token]);

        $postFields = [
            'previous_password' => 'aBc12345',
            'password'          => '12345'
        ];

        $this->client->users($user->id)->changePassword($postFields);
        $this->assertEquals(200, $this->client->getDebug()->lastResponseCode);
        $this->assertNull($this->client->getDebug()->lastResponseError);
    }

    /**
     * Tests delete user
     *
     * @depends testCreate
     */
    public function testDelete($user)
    {
        $this->client->users()->delete($user->id);
        $this->assertEquals(200, $this->client->getDebug()->lastResponseCode);
        $this->assertNull($this->client->getDebug()->lastResponseError);
    }
}
