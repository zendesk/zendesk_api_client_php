<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Users test class
 */
class UsersTest extends \PHPUnit_Framework_TestCase {

    private $client;
    private $subdomain;
    private $username;
    private $password;
    private $token;
    private $oAuthToken;

    public function __construct() {
        $this->subdomain = $GLOBALS['SUBDOMAIN'];
        $this->username = $GLOBALS['USERNAME'];
        $this->password = $GLOBALS['PASSWORD'];
        $this->token = $GLOBALS['TOKEN'];
        $this->oAuthToken = $GLOBALS['OAUTH_TOKEN'];
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
    }

    public function testCredentials() {
        $this->assertEquals($GLOBALS['SUBDOMAIN'] != '', true, 'Expecting GLOBALS[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['TOKEN'] != '', true, 'Expecting GLOBALS[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['USERNAME'] != '', true, 'Expecting GLOBALS[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $users = $this->client->users()->findAll();
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for requests[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $user = $this->client->user(454094082)->find(); // don't delete user #454094082
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }
    
    /**
     * @depends testAuthToken
     */
    public function testFindMultiple() {
        $users = $this->client->users(array(454094082))->find(); // Should add an additional user id here
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an array called "users"');
        $this->assertEquals(is_object($users->users[0]), true, 'Should return an object as first "users" array element');
    }

    /**
     * @depends testAuthToken
     */
    public function testRelated() {
        $related = $this->client->user(454094082)->related();
        $this->assertEquals(is_object($related), true, 'Should return an object');
        $this->assertEquals(is_object($related->user_related), true, 'Should return an object called "user_related"');
        $this->assertGreaterThan(0, $related->user_related->requested_tickets, 'Returns a non-numeric requested_tickets for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $user = $this->client->users()->create(array(
            'name' => 'Roger Wilco',
            'email' => 'roge@example.org',
            'verified' => true
        ));
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for user');
        $this->assertEquals($user->user->name, 'Roger Wilco', 'Name of test user does not match');
        $this->assertEquals($user->user->email, 'roge@example.org', 'Email of test user does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $user->user->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testAuthToken
     */
    public function testMerge() {
        $this->markTestSkipped(
            'Skipped for now because it may break my test login'
        );
        $user = $this->client->user(455060842)->merge(); // don't delete user #455060842
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreateMany() {
        $this->markTestSkipped(
            'Skipped for now because we have no way of cleaning up.'
        );
        $jobStatus = $this->client->users()->createMany(array(
            array(
                'name' => 'Roger Wilco',
                'email' => 'roge@example.org',
                'verified' => true
            ),
            array(
                'name' => 'Roger Wilco 2',
                'email' => 'roge2@example.org',
                'verified' => true
            ))
        );
        $this->assertEquals(is_object($jobStatus), true, 'Should return an object');
        $this->assertEquals(is_object($jobStatus->job_status), true, 'Should return an object called "job_status"');
        $this->assertGreaterThan(0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'CreateMany does not return HTTP code 200');
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $user = $this->client->user($id)->update(array(
            'name' => 'Joe Soap'
        ));
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($user->user->name, 'Joe Soap', 'Name of test user does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $user->user->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testSuspend(array $stack) {
        $id = array_pop($stack);
        $user = $this->client->user($id)->suspend();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $user->user->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $form = $this->client->user($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testAuthToken
     */
    public function testSearch() {
        $users = $this->client->users()->search(array('query' => 'Roger'));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAutocomplete() {
        $users = $this->client->users()->autocomplete(array('name' => 'rog'));
        $this->assertEquals(is_object($users), true, 'Should return an object');
        $this->assertEquals(is_array($users->users), true, 'Should return an object containing an array called "users"');
        $this->assertGreaterThan(0, $users->users[0]->id, 'Returns a non-numeric id for user');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testUpdateProfileImage() {
        $user = $this->client->user(454094082)->updateProfileImage(array(
            'file' => getcwd().'/tests/assets/UK.png'
        ));
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAuthenticatedUser() {
        $user = $this->client->users()->me();
        $this->assertEquals(is_object($user), true, 'Should return an object');
        $this->assertEquals(is_object($user->user), true, 'Should return an object called "user"');
        $this->assertGreaterThan(0, $user->user->id, 'Returns a non-numeric id for request');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testSetPassword() {
        $user = $this->client->user(454094082)->setPassword(array('password' => '12345'));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');        
    }

    /**
     * @depends testAuthToken
     */
    public function testChangePassword() {
        $user = $this->client->user(454094082)->changePassword(array('previous_password' => '12345', 'password' => '12346'));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');        
    }    

}

?>
