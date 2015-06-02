<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * TopicVotes test class
 */
class TopicVotesTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $forum_id, $topic_id;

    public function setUp()
    {
        /*
         * First start by creating forum and a topic (we'll delete it later)
         */
        $forum = $this->client->forums()->create(array(
            'name' => 'My Forum',
            'forum_type' => 'articles',
            'access' => 'logged-in users'
        ));
        $this->forum_id = $forum->forum->id;

        $topic = $this->client->topics()->create(array(
            'forum_id' => $this->forum_id,
            'title' => 'My Topic',
            'body' => 'This is a test topic'
        ));
        $this->topic_id = $topic->topic->id;
        $this->assertEquals(is_object($topic), true, 'Should return an object');
        $this->assertEquals(is_object($topic->topic), true, 'Should return an object called "topic"');
        $this->assertGreaterThan(0, $this->topic_id, 'Returns a non-numeric id for topic');
        /*
         * Continue with the rest of the test...
         */
        $topicVote = $this->client->topic($this->topic_id)->votes()->create(array(// 'user_id' => $user_id
        ));
        $this->assertEquals(is_object($topicVote), true, 'Should return an object');
        $this->assertEquals(is_object($topicVote->topic_vote), true, 'Should return an object called "topic_vote"');
        $this->assertGreaterThan(0, $topicVote->topic_vote->id, 'Returns a non-numeric id for topic_vote');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $topicVote->topic_vote->id;
    }

    public function testAll()
    {
        $topicVotes = $this->client->topic($this->topic_id)->votes()->findAll();
        $this->assertEquals(is_object($topicVotes), true, 'Should return an object');
        $this->assertEquals(is_array($topicVotes->topic_votes), true,
            'Should return an object containing an array called "topic_votes"');
        $this->assertGreaterThan(0, $topicVotes->topic_votes[0]->id, 'Returns a non-numeric id for topic_votes[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $topicVote = $this->client->topic($this->topic_id)->vote($this->id)->find();
        $this->assertEquals(is_object($topicVote), true, 'Should return an object');
        $this->assertGreaterThan(0, $topicVote->topic_vote->id, 'Returns a non-numeric id for topic_vote');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a topic vote id to test with. Did setUp fail?');
        $topicSubscription = $this->client->topic($this->topic_id)->votes()->delete(); // oddly enough, delete works by topic_id not vote_id
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        /*
         * Clean-up
         */
        $forum = $this->client->forum($this->forum_id)->delete();
    }

}
