<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Forums test class
 */
class ForumsTest extends BasicTest
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
        $forum = $this->client->forums()->create(array(
            'name' => 'My Forum',
            'forum_type' => 'articles',
            'access' => 'logged-in users'
        ));
        $this->assertEquals(is_object($forum), true, 'Should return an object');
        $this->assertEquals(is_object($forum->forum), true, 'Should return an object called "forum"');
        $this->assertGreaterThan(0, $forum->forum->id, 'Returns a non-numeric id for forum');
        $this->assertEquals($forum->forum->name, 'My Forum', 'Name of test forum does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $forum->forum->id;
    }

    public function testAll()
    {
        $forums = $this->client->forums()->findAll();
        $this->assertEquals(is_object($forums), true, 'Should return an object');
        $this->assertEquals(is_array($forums->forums), true,
            'Should return an object containing an array called "forums"');
        $this->assertGreaterThan(0, $forums->forums[0]->id, 'Returns a non-numeric id for forums[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $forum = $this->client->forum($this->id)->find();
        $this->assertEquals(is_object($forum), true, 'Should return an object');
        $this->assertGreaterThan(0, $forum->forum->id, 'Returns a non-numeric id for forum');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $forum = $this->client->forum($this->id)->update(array(
            'name' => 'My Forum II'
        ));
        $this->assertEquals(is_object($forum), true, 'Should return an object');
        $this->assertEquals(is_object($forum->forum), true, 'Should return an object called "forum"');
        $this->assertGreaterThan(0, $forum->forum->id, 'Returns a non-numeric id for forum');
        $this->assertEquals($forum->forum->name, 'My Forum II', 'Name of test forum does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a forum id to test with. Did setUp fail?');
        $view = $this->client->forum($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
