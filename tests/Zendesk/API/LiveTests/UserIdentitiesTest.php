<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * UserIdentities test class
 */
class UserIdentitiesTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $user_id, $number;

    public function setUp()
    {
        $this->number = strval(time());

        $user = $this->client->users()->create(array(
            'name' => 'Roger Wilco' . $this->number,
            'email' => 'roge' . $this->number . '@example.org',
            'role' => 'agent',
            'verified' => true
        ));
        $this->user_id = $user->user->id;

        $identity = $this->client->user($this->user_id)->identities()->create(array(
            'type' => 'email',
            'value' => 'devaris.brown' . $this->number . '@zendesk.com'
        ));
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertEquals(is_object($identity->identity), true, 'Should return an object called "identity"');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($identity->identity->value, 'devaris.brown' . $this->number . '@zendesk.com',
            'Title of test identity does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $identity->identity->id;
    }

    public function testCreateAsEndUser()
    {

        $user = $this->client->users()->create(array(
            'name' => 'Roger EndUser' . $this->number,
            'email' => 'roge2@example.org',
            'role' => 'end-user',
            'verified' => true
        ));

        $this->username = "roge2@example.org";
        $identity = $this->client->user($user->user->id)->identities()->create(array(
            'type' => 'email',
            'value' => 'foo@bar.com'
        ));
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertEquals(is_object($identity->identity), true, 'Should return an object called "identity"');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($identity->identity->value, 'foo@bar.com', 'Title of test identity does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->username = getenv('USERNAME');

        $this->client->user($user->user->id)->delete();
    }

    public function testAll()
    {
        $identities = $this->client->user($this->user_id)->identities()->findAll();
        $this->assertEquals(is_object($identities), true, 'Should return an object');
        $this->assertEquals(is_array($identities->identities), true,
            'Should return an object containing an array called "identities"');
        $this->assertGreaterThan(0, $identities->identities[0]->id, 'Returns a non-numeric id for identities[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $identity = $this->client->user($this->user_id)->identity($this->id)->find();
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for identity');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testMarkAsVerified()
    {
        $identity = $this->client->user($this->user_id)->identity($this->id)->markAsVerified();
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for identity');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testMakePrimary()
    {
        $identities = $this->client->user($this->user_id)->identity($this->id)->makePrimary();
        $this->assertEquals(is_object($identities), true, 'Should return an object');
        $this->assertGreaterThan(0, $identities->identities[0]->id, 'Returns a non-numeric id for identities[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $identities->identities[0]->id;
    }

    public function testRequestVerification()
    {
        $identity = $this->client->user($this->user_id)->identity($this->id)->requestVerification();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a identity id to test with. Did setUp fail?');
        $view = $this->client->user($this->user_id)->identity($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        $this->client->user($this->user_id)->delete();
    }

}
