<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Forums test class
 */
class ForumsTest extends \PHPUnit_Framework_TestCase {

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
    public function testAll() {
        $forums = $this->client->forums()->findAll();
        $this->assertEquals(is_object($forums), true, 'Should return an object');
        $this->assertEquals(is_array($forums->forums), true, 'Should return an object containing an array called "forums"');
        $this->assertGreaterThan(0, $forums->forums[0]->id, 'Returns a non-numeric id for forums[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $forum = $this->client->forum(22480662)->find(); // don't delete forum #22480662
        $this->assertEquals(is_object($forum), true, 'Should return an object');
        $this->assertGreaterThan(0, $forum->forum->id, 'Returns a non-numeric id for forum');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
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
        $id = $forum->forum->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $forum = $this->client->forum($id)->update(array(
            'name' => 'My Forum II'
        ));
        $this->assertEquals(is_object($forum), true, 'Should return an object');
        $this->assertEquals(is_object($forum->forum), true, 'Should return an object called "forum"');
        $this->assertGreaterThan(0, $forum->forum->id, 'Returns a non-numeric id for forum');
        $this->assertEquals($forum->forum->name, 'My Forum II', 'Name of test forum does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a forum id to test with. Did testCreate fail?');
        $view = $this->client->forum($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
