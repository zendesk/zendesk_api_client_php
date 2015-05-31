<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * OAuthClients test class
 */
class OAuthClientsTest extends BasicTest
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

    public function setUP()
    {
        $this->number = strval(time());
        $user = $this->client->users()->create(array(
            'name' => 'Roger Wilco' . $this->number,
            'email' => 'roge' . $this->number . '@example.org',
            'role' => 'agent',
            'verified' => true
        ));
        $this->user_id = $user->user->id;

        $client = $this->client->oauthClients()->create(array(
            'name' => 'Test Client' . $this->number,
            'identifier' => md5(time()),
            'user_id' => $this->user_id
        ));
        $this->assertEquals(is_object($client), true, 'Should return an object');
        $this->assertEquals(is_object($client->client), true, 'Should return an object called "client"');
        $this->assertGreaterThan(0, $client->client->id, 'Returns a non-numeric id for client');
        $this->assertEquals($client->client->name, 'Test Client' . $this->number, 'Name of test client does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $client->client->id;
    }

    public function testAll()
    {
        $clients = $this->client->oauthClients()->findAll();
        $this->assertEquals(is_object($clients), true, 'Should return an object');
        $this->assertEquals(is_array($clients->clients), true,
            'Should return an object containing an array called "clients"');
        $this->assertGreaterThan(0, $clients->clients[0]->id, 'Returns a non-numeric id for clients[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $client = $this->client->oauthClient($this->id)->find();
        $this->assertEquals(is_object($client), true, 'Should return an object');
        $this->assertGreaterThan(0, $client->client->id, 'Returns a non-numeric id for client');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $client = $this->client->oauthClient($this->id)->update(array(
            'name' => 'New Client Name' . $this->number
        ));
        $this->assertEquals(is_object($client), true, 'Should return an object');
        $this->assertEquals(is_object($client->client), true, 'Should return an object called "client"');
        $this->assertGreaterThan(0, $client->client->id, 'Returns a non-numeric id for client');
        $this->assertEquals($client->client->name, 'New Client Name' . $this->number,
            'Name of test client does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a client id to test with. Did setUp fail?');
        $topic = $this->client->oauthClient($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        $this->client->user($this->user_id)->delete();
    }

}
