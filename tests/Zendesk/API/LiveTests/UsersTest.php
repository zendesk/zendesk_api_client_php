<?php

namespace Zendesk\API\LiveTests;

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
        $userFields = [
            'name'  => 'Roger Wilco',
            'email' => 'roge' . time() . '@example.org',
        ];
        $response   = $this->client->users()->create($userFields);
        $this->assertTrue(property_exists($response, 'user'));

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
     * Tests update
     *
     * @depends testCreate
     */
    public function testUpdate($user)
    {
        $userFields = [
            'name' => 'Roger Wilco updated' . time(),
        ];

        $response = $this->client->users()->update($user->id, $userFields);

        $this->assertEquals($userFields['name'], $response->user->name);
    }

    /**
     * Tests update
     *
     * @depends testCreate
     */
    public function testDelete($user)
    {
        try {
            $this->client->users()->delete($user->id);
        } catch (\Exception $e) {
            $this->fail('An exception was not expected. Exception thrown was ' . $e->__toString());
        }
    }
}
