<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * TopicComments test class
 */
class TopicCommentsTest extends \PHPUnit_Framework_TestCase {

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
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        /*
         * First start by creating a topic (we'll delete it later)
         */
        $topic = $this->client->topics()->create(array(
            'forum_id' => 22480662,
            'title' => 'My Topic',
            'body' => 'This is a test topic'
        ));
        $this->assertEquals(is_object($topic), true, 'Should return an object');
        $this->assertEquals(is_object($topic->topic), true, 'Should return an object called "topic"');
        $this->assertGreaterThan(0, $topic->topic->id, 'Returns a non-numeric id for topic');
        /*
         * Continue with the rest of the test...
         */
        $topicComment = $this->client->topic($topic->topic->id)->comments()->create(array(
            'body' => 'A man walks into a bar'
        ));
        $this->assertEquals(is_object($topicComment), true, 'Should return an object');
        $this->assertEquals(is_object($topicComment->topic_comment), true, 'Should return an object called "topic_comment"');
        $this->assertGreaterThan(0, $topicComment->topic_comment->id, 'Returns a non-numeric id for topic_comment');
        $this->assertEquals($topicComment->topic_comment->body, 'A man walks into a bar', 'Body of test comment does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $topicComment->topic_comment->id;
        $stack = array($id, $topic->topic->id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $topicComments = $this->client->topic($stack[1])->comments()->findAll();
        $this->assertEquals(is_object($topicComments), true, 'Should return an object');
        $this->assertEquals(is_array($topicComments->topic_comments), true, 'Should return an object containing an array called "topic_comments"');
        $this->assertGreaterThan(0, $topicComments->topic_comments[0]->id, 'Returns a non-numeric id for topic_comments[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $topicComment = $this->client->topic($stack[1])->comment($stack[0])->find();
        $this->assertEquals(is_object($topicComment), true, 'Should return an object');
        $this->assertGreaterThan(0, $topicComment->topic_comment->id, 'Returns a non-numeric id for topic');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $topicComment = $this->client->topic($stack[1])->comment($stack[0])->update(array(
            'body' => 'A man walks into a different bar'
        ));
        $this->assertEquals(is_object($topicComment), true, 'Should return an object');
        $this->assertEquals(is_object($topicComment->topic_comment), true, 'Should return an object called "topic_comment"');
        $this->assertGreaterThan(0, $topicComment->topic_comment->id, 'Returns a non-numeric id for topic_comment');
        $this->assertEquals($topicComment->topic_comment->body, 'A man walks into a different bar', 'Name of test topic does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $this->assertGreaterThan(0, $stack[0], 'Cannot find a topic comment id to test with. Did testCreate fail?');
        $view = $this->client->topic($stack[1])->comment($stack[0])->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        /*
         * Clean-up
         */
        $topic = $this->client->topic($stack[1])->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
