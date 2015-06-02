<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Users test class
 */
class UsersTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $id_s, $ticket_id, $number, $external_id, $external_id_s;

    public function setUp()
    {
        $this->number = strval(time());

        $user = $this->client->users()->create(array(
            'name' => 'Roger Wilco' . $this->number,
            'email' => 'roge' . $this->number . '@example.org',
            'role' => 'agent',
            'verified' => true,
            'external_id' => '3000'
        ));
        $this->id = $user->user->id;
        $this->external_id = $user->user->external_id;

        $testTicket = array(
            'subject' => 'User Test',
            'comment' => array(
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal',
            'requester_id' => $this->id,
            'submitter_id' => $this->id
        );
        $ticket = $this->client->tickets()->create($testTicket);
        $this->ticket_id = $ticket->ticket->id;

        $user_s = $this->client->users()->create(array(
            'name' => 'Roger Wilco2' . $this->number,
            'email' => 'roge2' . $this->number . '@example.org',
            'role' => 'agent',
            'verified' => true,
            'external_id' => '3001'
        ));
        $this->id_s = $user_s->user->id;
        $this->external_id_s = $user_s->user->external_id;

        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for user');
        $this->assertEquals($user->user->name, 'Roger Wilco' . $this->number, 'Name of test user does not match');
        $this->assertEquals($user->user->email, 'roge' . $this->number . '@example.org',
            'Email of test user does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

    public function testAll()
    {
        $users = $this->client->users()->findAll();
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true,
            'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for requests[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $user = $this->client->user($this->id)->find();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFindMultiple()
    {
        $users = $this->client->users(array($this->id, $this->id_s))->find();
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(is_object($users->users[0]), true,
            'Should return an object as first "users" array element');
    }

    public function testShowManyUsingIds()
    {
        $users = $this->client->users()->showMany(array('ids' => array($this->id, $this->id_s)));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(is_object($users->users[0]), true,
            'Should return an object as first "users" array element');
    }

    public function testShowManyUsingExternalIds()
    {
        $users = $this->client->users()->showMany(array(
            'external_ids' => array(
                $this->external_id,
                $this->external_id_s
            )
        ));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(is_object($users->users[0]), true,
            'Should return an object as first "users" array element');
    }

    public function testRelated()
    {
        $related = $this->client->user($this->id)->related();
        $this->assertEquals(is_object($related), true, 'Should return an object');
        $this->assertEquals(is_object($related->user_related), true, 'Should return an object called "user_related"');
        $this->assertGreaterThan(0, $related->user_related->requested_tickets,
            'Returns a non-numeric requested_tickets for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testMerge()
    {
        // create users to merge
        $mergeFrom1Email = 'mergefrom1.' . $this->number . '@example.org';
        $user_m = $this->client->users()->create(array(
            'name' => 'Merge From1' . $this->number,
            'email' => $mergeFrom1Email,
            'role' => 'end-user',
            'verified' => true
        ));
        $mergeFrom1 = $user_m->user->id;
        $user_m = $this->client->users()->create(array(
            'name' => 'Merge From2' . $this->number,
            'email' => 'mergefrom2.' . $this->number . '@example.org',
            'role' => 'end-user',
            'verified' => true
        ));
        $mergeFrom2 = $user_m->user->id;
        $mergeToEmail = 'mergeto.' . $this->number . '@example.org';
        $user_m = $this->client->users()->create(array(
            'name' => 'Merge To' . $this->number,
            'email' => $mergeToEmail,
            'role' => 'end-user',
            'verified' => true
        ));
        $mergeTo = $user_m->user->id;

        // set password for mergeFrom1 to be able to authenticate as end-user
        $passwordFrom1 = "aBc12345";
        $user = $this->client->user($mergeFrom1)->setPassword(array('password' => $passwordFrom1));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        // set password for mergeTo to be able to merge 'me' to
        $passwordMergeTo = "abC12345";
        $user = $this->client->user($mergeTo)->setPassword(array('password' => $passwordMergeTo));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        $username = $mergeFrom1Email;
        $client_end_user = new Client($this->subdomain, $username);
        $client_end_user->setAuth('password', $passwordFrom1);

        // test merge 'me' to user 'mergeTo'
        $user = $client_end_user->users()->merge(['email' => $mergeToEmail, 'password' => $passwordMergeTo]);
        $this->assertEquals(true, is_object($user), 'Should return an object');
        $this->assertEquals(true, is_object($user->user), 'Should return an object called "user"');
        $this->assertEquals($mergeTo, $user->user->id, 'Returned user should be the one merged to.');
        $this->assertEquals('200', $this->client->getDebug()->lastResponseCode, 'Does not return HTTP code 200');

        // test merge user 'mergeFrom2' to user 'mergeTo'
        $user = $this->client->user($mergeFrom2)->merge(['id' => $mergeTo]);
        $this->assertEquals(true, is_object($user), 'Should return an object');
        $this->assertEquals(true, is_object($user->user), 'Should return an object called "user"');
        $this->assertEquals($mergeTo, $user->user->id, 'Returned user should be the one merged to.');
        $this->assertEquals('200', $this->client->getDebug()->lastResponseCode, 'Does not return HTTP code 200');

        // clean up
        $user = $this->client->user($mergeTo)->delete();
        $this->assertEquals('200', $this->client->getDebug()->lastResponseCode, 'Does not return HTTP code 200');
    }

    public function testCreateMany()
    {
        $this->markTestSkipped(
            'Skipped for now because we have no way of cleaning up.'
        );
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
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200',
            'CreateMany does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $user = $this->client->user($this->id)->update(array(
            'name' => 'Joe Soap'
        ));
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($user->user->name, 'Joe Soap', 'Name of test user does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdateMany()
    {
        $jobStatus = $this->client->users()->updateMany(array(
            'ids' => $this->id . ',' . $this->id_s,
            'phone' => '1234567890'
        ));
        $this->assertEquals(is_object($jobStatus), true, 'Should return an array');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200',
            'CreateMany does not return HTTP code 200');
    }

    public function testUpdateManyIndividualUsers()
    {
        $jobStatus = $this->client->users()->updateManyIndividualUsers(array(
            array(
                'id' => $this->id,
                'phone' => '1234567890'
            ),
            array(
                'id' => $this->id_s,
                'phone' => '0987654321'
            )
        ));
        $this->assertEquals(is_object($jobStatus), true, 'Should return an array');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200',
            'CreateMany does not return HTTP code 200');
    }

    public function testSuspend()
    {
        $user = $this->client->user($this->id)->suspend();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testSearch()
    {
        $users = $this->client->users()->search(array('query' => 'Roger'));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true,
            'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /*
     * Needs an existed User with specified query 'name' keyword to run this function
     */
    public function testAutocomplete()
    {
        $users = $this->client->users()->autocomplete(array('name' => 'joh'));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true,
            'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdateProfileImage()
    {
        $user = $this->client->user($this->id)->updateProfileImage(array(
            'file' => getcwd() . '/tests/assets/UK.png'
        ));
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAuthenticatedUser()
    {
        $user = $this->client->users()->me();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testSetPassword()
    {
        $user = $this->client->user($this->id)->setPassword(array('password' => "aBc12345"));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testChangePassword()
    {
        $this->markTestSkipped(
            'Skipped for now because you can only change password of your own account.'
        );
        $user = $this->client->user(421450109)->changePassword(array(
            'previous_password' => '12346',
            'password' => '12345'
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->client->ticket($this->ticket_id)->delete();

        $user = $this->client->user($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $user_s = $this->client->user($this->id_s)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

    }

}
