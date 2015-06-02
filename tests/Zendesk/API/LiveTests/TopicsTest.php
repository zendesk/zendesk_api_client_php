<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Topics test class
 */
class TopicsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $forum_id;

    public function setUP()
    {
        /*
         * First start by creating a forum and a topic (we'll delete them later)
         */
        $forum = $this->client->forums()->create(array(
            'name' => 'My Forum',
            'forum_type' => 'articles',
            'access' => 'logged-in users'
        ));
        $this->forum_id = $forum->forum->id;
        /*
         * Continue with the rest of the test...
         */
        $topic = $this->client->topics()->create(array(
            'forum_id' => $this->forum_id,
            'title' => 'My Topic',
            'body' => 'This is a test topic'
        ));
        $this->assertEquals(is_object($topic), true, 'Should return an object');
        $this->assertEquals(is_object($topic->topic), true, 'Should return an object called "topic"');
        $this->assertGreaterThan(0, $topic->topic->id, 'Returns a non-numeric id for topic');
        $this->assertEquals($topic->topic->title, 'My Topic', 'Name of test topic does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $topic->topic->id;
    }

    public function testAll()
    {
        $topics = $this->client->topics()->findAll();
        $this->assertEquals(is_object($topics), true, 'Should return an object');
        $this->assertEquals(is_array($topics->topics), true,
            'Should return an object containing an array called "topics"');
        $this->assertGreaterThan(0, $topics->topics[0]->id, 'Returns a non-numeric id for topics[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $topic = $this->client->topic($this->id)->find();
        $this->assertEquals(is_object($topic), true, 'Should return an object');
        $this->assertGreaterThan(0, $topic->topic->id, 'Returns a non-numeric id for topic');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $topic = $this->client->topic($this->id)->update(array(
            'title' => 'My Topic II'
        ));
        $this->assertEquals(is_object($topic), true, 'Should return an object');
        $this->assertEquals(is_object($topic->topic), true, 'Should return an object called "topic"');
        $this->assertGreaterThan(0, $topic->topic->id, 'Returns a non-numeric id for topic');
        $this->assertEquals($topic->topic->title, 'My Topic II', 'Name of test topic does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a topic id to test with. Did setUp fail?');
        $topic = $this->client->topic($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        /*
         * Clean-up
         */
        $topic = $this->client->forum($this->forum_id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
