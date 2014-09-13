<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * UserIdentities test class
 */
class UserIdentitiesTest extends \PHPUnit_Framework_TestCase {

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
        $identities = $this->client->user(454094082)->identities()->findAll();
        $this->assertEquals(is_object($identities), true, 'Should return an object');
        $this->assertEquals(is_array($identities->identities), true, 'Should return an object containing an array called "identities"');
        $this->assertGreaterThan(0, $identities->identities[0]->id, 'Returns a non-numeric id for identities[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $identity = $this->client->user(454094082)->identity(463568382)->find(); // don't delete identity #1
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for identity');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreateAsEndUser() {
        $this->markTestSkipped(
            'Skipped for now because I need to get a new user account'
        );
        $this->username = "roge2@example.org";
        $identity = $this->client->user(454094082)->identities()->create(array(
            'end_user' => true,
            'type' => 'email',
            'value' => 'foo@bar.com',
            'verified' => true
        ));
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertEquals(is_object($identity->identity), true, 'Should return an object called "identity"');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($identity->identity->value, 'foo@bar.com', 'Title of test identity does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->username = $GLOBALS['USERNAME'];
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $identity = $this->client->user(454094082)->identities()->create(array(
            'type' => 'email',
            'value' => 'devaris.brown@zendesk.com'
        ));
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertEquals(is_object($identity->identity), true, 'Should return an object called "identity"');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($identity->identity->value, 'devaris.brown@zendesk.com', 'Title of test identity does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $identity->identity->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testMarkAsVerified(array $stack) {
        $id = array_pop($stack);
        $identity = $this->client->user(454094082)->identity($id)->markAsVerified();
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for identity');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $identity->identity->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testMakePrimary(array $stack) {
        $id = array_pop($stack);
        $identities = $this->client->user(454094082)->identity($id)->makePrimary();
        $this->assertEquals(is_object($identities), true, 'Should return an object');
        $this->assertGreaterThan(0, $identities->identities[0]->id, 'Returns a non-numeric id for identities[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $identities->identities[0]->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testRequestVerification(array $stack) {
        $id = array_pop($stack);
        $identity = $this->client->user(454094082)->identity($id)->requestVerification();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a identity id to test with. Did testCreate fail?');
        $view = $this->client->user(454094082)->identity($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }


}

?>
