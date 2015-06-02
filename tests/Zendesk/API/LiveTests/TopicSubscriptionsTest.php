<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * TopicSubscriptions test class
 */
class TopicSubscriptionsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $topic_id, $forum_id, $user_id, $number;

    public function setUp()
    {
        /*
        * First start by creating a forum and a topic (we'll delete them later)
        */
        $this->number = strval(time());
        $forum = $this->client->forums()->create(array(
            'name' => 'My Forum',
            'forum_type' => 'articles',
            'access' => 'logged-in users'
        ));
        $this->forum_id = $forum->forum->id;

        $user = $this->client->users()->create(array(
            'name' => 'Roger Wilco' . $this->number,
            'email' => 'roge' . $this->number . '@example.org',
            'verified' => true
        ));
        $this->user_id = $user->user->id;

        $topic = $this->client->topics()->create(array(
            'forum_id' => $this->forum_id,
            'title' => 'My Topic',
            'body' => 'This is a test topic'
        ));
        $this->topic_id = $topic->topic->id;
        /*
         * Continue with the rest of the test...
         */
        $topicSubscription = $this->client->topic($topic->topic->id)->subscriptions()->create(array(
            'user_id' => $this->user_id
        ));
        $this->assertEquals(is_object($topicSubscription), true, 'Should return an object');
        $this->assertEquals(is_object($topicSubscription->topic_subscription), true,
            'Should return an object called "topic_subscription"');
        $this->assertGreaterThan(0, $topicSubscription->topic_subscription->id,
            'Returns a non-numeric id for topic_subscription');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $topicSubscription->topic_subscription->id;
    }

    public function testAll()
    {
        $topicSubscriptions = $this->client->topic($this->topic_id)->subscriptions()->findAll();
        $this->assertEquals(is_object($topicSubscriptions), true, 'Should return an object');
        $this->assertEquals(is_array($topicSubscriptions->topic_subscriptions), true,
            'Should return an object containing an array called "topic_subscriptions"');
        $this->assertGreaterThan(0, $topicSubscriptions->topic_subscriptions[0]->id,
            'Returns a non-numeric id for topic_subscriptions[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $topicSubscription = $this->client->topic($this->topic_id)->subscription($this->id)->find();
        $this->assertEquals(is_object($topicSubscription), true, 'Should return an object');
        $this->assertGreaterThan(0, $topicSubscription->topic_subscription->id, 'Returns a non-numeric id for topic');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function teardown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a topic subscription id to test with. Did setUp fail?');
        $topicSubscription = $this->client->topic($this->topic_id)->subscription($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        /*
         * Clean-up
         */
        $topic = $this->client->topic($this->topic_id)->delete();
        $forum = $this->client->forum($this->forum_id)->delete();
        $user = $this->client->user($this->user_id)->delete();
    }

}
