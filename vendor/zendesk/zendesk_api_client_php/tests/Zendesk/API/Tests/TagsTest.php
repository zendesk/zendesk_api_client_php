<?php
// FINISH THIS!

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Tags test class
 */
class TagsTest extends \PHPUnit_Framework_TestCase {

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
        $tags = $this->client->ticket(1)->tags()->create(array('tags' => array('important')));
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('important', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

    /**
     * @depends testCreate
     */
    public function testAll() {
        $tags = $this->client->tags()->findAll();
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testCreate
     */
    public function testUpdate() {
        $tags = $this->client->ticket(1)->tags()->update(array('tags' => array('customer')));
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('customer', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testCreate
     */
    public function testFind() {
        $tags = $this->client->ticket(1)->tags()->find();
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('customer', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testCreate
     */
    public function testDelete() {
        $tags = $this->client->ticket(1)->tags()->delete(array('tags' => array('customer')));
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('important', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
