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
    private $endusername;
    private $rand;
    private $enduserclient;

    public function __construct() {
        $this->subdomain = $GLOBALS['SUBDOMAIN'];
        $this->username = $GLOBALS['USERNAME'];
        $this->endusername = $GLOBALS['END_USER_USERNAME'];
        $this->password = $GLOBALS['PASSWORD'];
        $this->token = $GLOBALS['TOKEN'];
        $this->oAuthToken = $GLOBALS['OAUTH_TOKEN'];
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
        //$this->enduserclient = new Client($this->subdomain, $this->endusername);
        //$this->enduserclient->setAuth('token', $this->token);
        $this->rand = mt_rand(5, 650000);
        //$this->enduserclient = new Client($this->subdomain, 'testuser'.$this->rand.'@example.org');
        //$this->enduserclient->setAuth('token', $this->token);
       // $this->user_id = $this->client->users()->find(array('id' => ))
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

    public function testCreateUser() {
	    $email = 'testuser'.$this->rand.'@example.org';
        $user = $this->client->users()->create(array(
            'name' => 'Test User #'.$this->rand,
            'email' => $email,
            'verified' => true
        ));
        $id = $user->user->id;
        $identities = $this->client->userIdentities()->findAll(array('user_id' => $id));
        $stack = array($email, $id);
        return $stack;
    }
    
     /**
     * @depends testCreateUser
     */
    public function testCreateAsEndUser(array $stack) {
        /*$this->markTestSkipped(
            'Skipped for now because I need to get a new user account'
        );*/
        $id = array_pop($stack);
        $email = array_pop($stack);       
        $enduserclient = new Client($this->subdomain, $email);
        $enduserclient->setAuth('token', $this->token);

        $identity = $enduserclient->userIdentities()->create(array(
            'user_id' => $id,//760981318,
            'end_user' => true,
            'type' => 'email',
            'value' => 'test-enduser'.$this->rand.'@example.org'
        ));
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertEquals(is_object($identity->identity), true, 'Should return an object called "identity"');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($identity->identity->value, 'test-enduser'.$this->rand.'@example.org', 'Title of test identity does not match');
        $this->assertEquals($enduserclient->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

    /**
     * @depends testCreateUser
     */
    public function testAll(array $stack) {
        $id = array_pop($stack);
        $identities = $this->client->userIdentities()->findAll(array('user_id' => $id));
        //$identities = $this->client->userIdentities()->findAll(array('user_id' => ))
        $this->assertEquals(is_object($identities), true, 'Should return an object');
        $this->assertEquals(is_array($identities->identities), true, 'Should return an object containing an array called "identities"');
        $this->assertGreaterThan(0, $identities->identities[0]->id, 'Returns a non-numeric id for identities[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($identities->identities[0]->id, $id);
        return $stack;
    }

    /**
     * @depends testAll
     */
    public function testFind(array $stack) {
        $id = array_pop($stack);
        $new_identity = array_pop($stack);
        $identities = $this->client->userIdentities()->find(array('user_id' => $id, 'id' => $new_identity));
        $this->assertEquals(is_object($identities), true, 'Should return an object');
        $this->assertGreaterThan(0, $identities->identity->id, 'Returns a non-numeric id for identity');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAll
     */
    public function testCreate(array $stack) {
        $id = array_pop($stack);
        $identity = $this->client->userIdentities()->create(array(
            'user_id' => $id,
            'type' => 'email',
            'value' => 'example-user'.$this->rand.'@example.org'
        ));
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertEquals(is_object($identity->identity), true, 'Should return an object called "identity"');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($identity->identity->value, 'example-user'.$this->rand.'@example.org', 'Title of test identity does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $identity_id = $identity->identity->id;
        $stack = array($identity_id, $id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testRequestVerification(array $stack) {
        $id = array_pop($stack);
        $new_identity = array_pop($stack);
        $identity = $this->client->userIdentities()->requestVerification(array('user_id' => $id, 'id' => $new_identity));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        //return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testMarkAsVerified(array $stack) {
        $id = array_pop($stack);
        $new_identity = array_pop($stack);
        $identity = $this->client->userIdentities()->markAsVerified(array('user_id' => $id, 'id' => $new_identity));
        $this->assertEquals(is_object($identity), true, 'Should return an object');
        $this->assertGreaterThan(0, $identity->identity->id, 'Returns a non-numeric id for identity');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $identity_id = $identity->identity->id;
        $stack = array($identity_id, $id);
        return $stack;
    }
    

    
    /**
     * @depends testCreate
     */
   public function testMakePrimary(array $stack) {
        $id = array_pop($stack);
        $new_identity = array_pop($stack);
        $identities = $this->client->userIdentities()->makePrimary(array('user_id' => $id, 'id' => $new_identity));
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
    public function testDelete(array $stack) {
        $user_id = array_pop($stack);
        $identity_id = array_pop($stack);
        $this->assertGreaterThan(0, $identity_id, 'Cannot find a identity id to test with. Did testCreate fail?');
        $this->client->userIdentities()->delete(array('user_id' => $user_id, 'id' => $identity_id));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }


}

?>
