<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Targets test class
 */
class TargetsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id;

    public function setUp()
    {
        $target = $this->client->targets()->create(array(
            'type' => 'email_target',
            'title' => 'Test Email Target',
            'email' => 'hello@example.com',
            'subject' => 'Test Target'
        ));
        $this->assertEquals(is_object($target), true, 'Should return an object');
        $this->assertEquals(is_object($target->target), true, 'Should return an object called "target"');
        $this->assertGreaterThan(0, $target->target->id, 'Returns a non-numeric id for target');
        $this->assertEquals($target->target->title, 'Test Email Target', 'Title of test target does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $target->target->id;
    }

    public function testAll()
    {
        $targets = $this->client->targets()->findAll();
        $this->assertEquals(is_object($targets), true, 'Should return an object');
        $this->assertEquals(is_array($targets->targets), true,
            'Should return an object containing an array called "targets"');
        $this->assertGreaterThan(0, $targets->targets[0]->id, 'Returns a non-numeric id for targets[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $target = $this->client->target($this->id)->find();
        $this->assertEquals(is_object($target), true, 'Should return an object');
        $this->assertGreaterThan(0, $target->target->id, 'Returns a non-numeric id for target');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $target = $this->client->target($this->id)->update(array(
            'email' => 'roger@example.com'
        ));
        $this->assertEquals(is_object($target), true, 'Should return an object');
        $this->assertEquals(is_object($target->target), true, 'Should return an object called "target"');
        $this->assertGreaterThan(0, $target->target->id, 'Returns a non-numeric id for target');
        $this->assertEquals($target->target->email, 'roger@example.com', 'Email of test target does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a target id to test with. Did setUp fail?');
        $result = $this->client->target($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
