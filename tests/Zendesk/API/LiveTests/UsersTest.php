<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Users test class
 */
class UsersTest extends BasicTest
{
    protected $number;

    public function testCreate()
    {
        $testUser = array(
            'id' => '12345',
            'name' => 'Roger Wilco',
            'email' => 'roge@example.org',
            'role' => 'agent',
            'verified' => true,
            'external_id' => '3000'
        );

        $this->mockApiCall('POST', '/users.json', array('user' => $testUser), array('code' => 201));

        $user = $this->client->users()->create($testUser);

        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for user');
    }

    public function testDelete()
    {
        $this->mockApiCall('DELETE', '/users/12345.json?', array());
        $this->client->users(12345)->delete();
    }

    public function testAll()
    {
        $this->mockApiCall('GET', '/users.json?', array('users' => array(array('id' => 12345))));

        $users = $this->client->users()->findAll();
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true,
            'Should return an object containing an array called "users"');

        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for requests[0]');
    }

    public function testFind()
    {
        $this->mockApiCall('GET', '/users/12345.json?', array('user' => array('id' => 12345)));

        $user = $this->client->user(12345)->find();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for user');
    }

    public function testFindMultiple()
    {
        $findIds = array(12345, 80085);
        $response = array('users' => array(
                array('id' => $findIds[0]),
                array('id' => $findIds[1]),
            )
        );

        $this->mockApiCall('GET', '/users/show_many.json?ids=' . implode(',', $findIds) . '&', $response);

        $users = $this->client->users($findIds)->find();
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals($users->users[0]->id, $findIds[0]);
        $this->assertEquals($users->users[1]->id, $findIds[1]);
    }

    public function testShowManyUsingIds()
    {
        $findIds = array(12345, 80085);
        $response = array('users' => array(
                array('id' => $findIds[0]),
                array('id' => $findIds[1]),
            )
        );

        $this->mockApiCall('GET', '/users/show_many.json?ids=' . implode(',', $findIds) . '&', $response);

        $users = $this->client->users()->showMany(array('ids' => $findIds));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(is_object($users->users[0]), true,
            'Should return an object as first "users" array element');
    }

    public function testShowManyUsingExternalIds()
    {
        $findIds = array(12345, 80085);
        $response = array('users' => array(
                array('id' => $findIds[0]),
                array('id' => $findIds[1]),
            )
        );

        $this->mockApiCall('GET', '/users/show_many.json?external_ids=' . implode(',', $findIds) . '&', $response);

        $users = $this->client->users()->showMany(array('external_ids' => $findIds));

        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(is_object($users->users[0]), true,
            'Should return an object as first "users" array element');
    }

    public function testRelated()
    {
        $this->mockApiCall('GET', '/users/12345/related.json?', array('user_related' => array('requested_tickets' => 1)));

        $related = $this->client->user(12345)->related();
        $this->assertEquals(is_object($related), true, 'Should return an object');
        $this->assertEquals(is_object($related->user_related), true, 'Should return an object called "user_related"');
        $this->assertGreaterThan(0, $related->user_related->requested_tickets,
            'Returns a non-numeric requested_tickets for user');
    }

    public function testMerge()
    {
        $this->mockApiCall('PUT', '/users/me/merge.json', array('user' => array('id' => 12345)));
        $this->client->user('me')->merge(array('id' => 12345));
    }

    public function testCreateMany()
    {
        $this->mockApiCall('POST', '/users/create_many.json', array('job_status' => array('id' => 1)));
        $jobStatus = $this->client->users()->createMany(array(
                array(
                    'name' => 'Roger Wilco 3',
                    'email' => 'roge3@example.org',
                    'verified' => true
                ),
                array(
                    'name' => 'Roger Wilco 4',
                    'email' => 'roge4@example.org',
                    'verified' => true
                )
            )
        );
        $this->assertEquals(is_object($jobStatus), true, 'Should return an object');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]');
    }

    public function testUpdate()
    {
        $this->mockApiCall('PUT', '/users/12345.json', array('user' => array()));
        $user = $this->client->user(12345)->update(array(
            'name' => 'Joe Soap'
        ));
    }

    public function testUpdateMany()
    {
        $updateIds = array(12345, 80085);
        $this->mockApiCall('PUT', '/users/update_many.json?ids=' . implode(',', $updateIds), array('job_status' => array('id' => 1)));
        $jobStatus = $this->client->users()->updateMany(array(
            'ids' => implode(',', $updateIds),
            'phone' => '1234567890'
        ));
        $this->assertEquals(is_object($jobStatus), true, 'Should return an array');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]');
    }

    public function testUpdateManyIndividualUsers()
    {
        $this->mockApiCall('PUT', '/users/update_many.json', array('job_status' => array('id' => 1)));
        $jobStatus = $this->client->users()->updateManyIndividualUsers(array(
            array(
                'id' => 12345,
                'phone' => '1234567890'
            ),
            array(
                'id' => 80085,
                'phone' => '0987654321'
            )
        ));
        $this->assertEquals(is_object($jobStatus), true, 'Should return an array');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]');
    }

    public function testSuspend()
    {
        $this->mockApiCall('PUT', '/users/12345.json', array('user' => array('id' => 12345)));
        $user = $this->client->user(12345)->suspend();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
    }

    public function testSearch()
    {
        $this->mockApiCall('GET', '/users/search.json?query=Roger&', array('users' => array(array('id' => 12345))));
        $users = $this->client->users()->search(array('query' => 'Roger'));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true,
            'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for user');
    }

    /*
     * Needs an existed User with specified query 'name' keyword to run this function
     */
    public function testAutocomplete()
    {
        $this->mockApiCall('POST', '/users/autocomplete.json?name=joh', array('users' => array(array('id' => 12345))));

        $users = $this->client->users()->autocomplete(array('name' => 'joh'));

        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true,
            'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for user');
    }

    public function testUpdateProfileImage()
    {
        $this->mockApiCall('GET', '/users/12345.json?', array('id' => 12345));
        $this->mockApiCall('PUT', '/users/12345.json', array('user' => array('id' => 12345)));

        $user = $this->client->user(12345)->updateProfileImage(array(
            'file' => getcwd() . '/tests/assets/UK.png'
        ));

        $contentType = $this->http->requests->first()->getHeader("Content-Type")->toArray()[0];
        $this->assertEquals($contentType, "application/binary");

        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
    }

    public function testAuthenticatedUser()
    {
        $this->mockApiCall('GET', '/users/me.json?', array('user' => array('id' => 12345)));
        $user = $this->client->users()->me();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
    }

    public function testSetPassword()
    {
        $this->mockApiCall('POST', '/users/12345/password.json', array());
        $user = $this->client->user(12345)->setPassword(array('password' => "aBc12345"));
    }

    public function testChangePassword()
    {
        $this->mockApiCall('PUT', '/users/421450109/password.json', array());
        $user = $this->client->user(421450109)->changePassword(array(
            'previous_password' => '12346',
            'password' => '12345'
        ));
    }

}
