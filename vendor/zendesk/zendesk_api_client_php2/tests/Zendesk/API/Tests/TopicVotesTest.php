<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * TopicVotes test class
 */
class TopicVotesTest extends \PHPUnit_Framework_TestCase {

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
        $topicVote = $this->client->topic($topic->topic->id)->votes()->create(array(
    //        'user_id' => 455060842
        ));
        $this->assertEquals(is_object($topicVote), true, 'Should return an object');
        $this->assertEquals(is_object($topicVote->topic_vote), true, 'Should return an object called "topic_vote"');
        $this->assertGreaterThan(0, $topicVote->topic_vote->id, 'Returns a non-numeric id for topic_vote');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $topicVote->topic_vote->id;
        $stack = array($id, $topic->topic->id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $topicVotes = $this->client->topic($stack[1])->votes()->findAll();
        $this->assertEquals(is_object($topicVotes), true, 'Should return an object');
        $this->assertEquals(is_array($topicVotes->topic_votes), true, 'Should return an object containing an array called "topic_votes"');
        $this->assertGreaterThan(0, $topicVotes->topic_votes[0]->id, 'Returns a non-numeric id for topic_votes[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $topicVote = $this->client->topic($stack[1])->vote($stack[0])->find();
        $this->assertEquals(is_object($topicVote), true, 'Should return an object');
        $this->assertGreaterThan(0, $topicVote->topic_vote->id, 'Returns a non-numeric id for topic_vote');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $this->assertGreaterThan(0, $stack[0], 'Cannot find a topic vote id to test with. Did testCreate fail?');
        $topicSubscription = $this->client->topic($stack[1])->votes()->delete(); // oddly enough, delete works by topic_id not vote_id
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        /*
         * Clean-up
         */
        $topic = $this->client->topic($stack[1])->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
