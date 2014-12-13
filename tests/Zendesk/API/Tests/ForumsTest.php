<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Forums test class
 */
class ForumsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
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
