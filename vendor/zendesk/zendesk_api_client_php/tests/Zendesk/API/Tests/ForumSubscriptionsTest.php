<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * ForumSubscriptions test class
 */
class ForumSubscriptionsTest extends \PHPUnit_Framework_TestCase {

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
