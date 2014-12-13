<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * ForumSubscriptions test class
 */
class ForumSubscriptionsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    /*
     * The order of this test differs because we don't have pre-existing subscriptions to test with.
     * So, we create first, then test find/findAll
     */

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $forumSubscription = $this->client->forum(22480662)->subscriptions()->create(array(
            'user_id' => 454094082
        ));
        $this->assertEquals(is_object($forumSubscription), true, 'Should return an object');
        $this->assertEquals(is_object($forumSubscription->forum_subscription), true, 'Should return an object called "forum_subscription"');
        $this->assertGreaterThan(0, $forumSubscription->forum_subscription->id, 'Returns a non-numeric id for forum_subscription');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $forumSubscription->forum_subscription->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $forumSubscriptions = $this->client->forums()->subscriptions()->findAll();
        $this->assertEquals(is_object($forumSubscriptions), true, 'Should return an object');
        $this->assertEquals(is_array($forumSubscriptions->forum_subscriptions), true, 'Should return an object containing an array called "forum_subscriptions"');
        $this->assertGreaterThan(0, $forumSubscriptions->forum_subscriptions[0]->id, 'Returns a non-numeric id for forum_subscriptions[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $forumSubscription = $this->client->forums()->subscription($id)->find(); // find the one we just created
        $this->assertEquals(is_object($forumSubscription), true, 'Should return an object');
        $this->assertGreaterThan(0, $forumSubscription->forum_subscription->id, 'Returns a non-numeric id for forum_subscription');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a forum subscription id to test with. Did testCreate fail?');
        $view = $this->client->forums()->subscription($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
