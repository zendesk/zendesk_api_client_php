<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Voice test class
 */
class VoiceTest extends \PHPUnit_Framework_TestCase {

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
    public function testCreatePhoneNumber() {
        // First we need to search for an available phone number
        $numbers = $this->client->voice()->phoneNumbers()->search(array(
            'country' => 'US'
        ));
        $this->assertEquals(is_object($numbers), true, 'Should return an object');
        $this->assertEquals(is_array($numbers->phone_numbers), true, 'Should return an object containing an array called "phone_numbers"');
        $this->assertEquals(is_string($numbers->phone_numbers[0]->token), true, 'No string token found for first phone number');
        // Now we assign it to our account
        $number = $this->client->voice()->phoneNumbers()->create(array(
            'token' => $numbers->phone_numbers[0]->token
        ));
        $this->assertEquals(is_object($number), true, 'Should return an object');
        $this->assertEquals(is_object($number->phone_number), true, 'Should return an object called "phone_number"');
        $this->assertGreaterThan(0, $number->phone_number->id, 'Returns a non-numeric id for trigger');
        $this->assertEquals($number->phone_number->number, $numbers->phone_numbers[0]->number, 'Value of test phone number does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $number->phone_number->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreatePhoneNumber
     */
    public function testAllPhoneNumber($stack) {
        $numbers = $this->client->voice()->phoneNumbers()->findAll();
        $this->assertEquals(is_object($numbers), true, 'Should return an object');
        $this->assertEquals(is_array($numbers->phone_numbers), true, 'Should return an object containing an array called "phone_numbers"');
        $this->assertGreaterThan(0, $numbers->phone_numbers[0]->id, 'Returns a non-numeric id for phone_numbers[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreatePhoneNumber
     */
    public function testSearchPhoneNumber($stack) {
        $numbers = $this->client->voice()->phoneNumbers()->search(array('country' => 'US'));
        $this->assertEquals(is_object($numbers), true, 'Should return an object');
        $this->assertEquals(is_array($numbers->phone_numbers), true, 'Should return an object containing an array called "phone_numbers"');
        $this->assertEquals(is_string($numbers->phone_numbers[0]->token), true, 'No string token found for first phone number');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreatePhoneNumber
     */
    public function testFindPhoneNumber($stack) {
        $id = array_pop($stack);
        $number = $this->client->voice()->phoneNumber($id)->find();
        $this->assertGreaterThan(0, $number->phone_number->id, 'Returns a non-numeric id for phone_number');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreatePhoneNumber
     */
    public function testUpdatePhoneNumber(array $stack) {
        $id = array_pop($stack);
        $number = $this->client->voice()->phoneNumber($id)->update(array(
            'nickname' => 'Awesome support line'
        ));
        $this->assertEquals(is_object($number), true, 'Should return an object');
        $this->assertEquals(is_object($number->phone_number), true, 'Should return an object called "phone_number"');
        $this->assertGreaterThan(0, $number->phone_number->id, 'Returns a non-numeric id for phone_number');
        $this->assertEquals($number->phone_number->nickname, 'Awesome support line', 'Nickname of test phone number does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreatePhoneNumber
     */
    public function testDeletePhoneNumber(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a phone number id to test with. Did testCreate fail?');
        $result = $this->client->voice()->phoneNumber($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete trigger does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreateGreeting() {
        $greeting = $this->client->voice()->greetings()->create(array(
            'name' => 'Hello',
            'type' => 'Voicemail',
            'category_id' => 1
        ));
        $this->assertEquals(is_object($greeting), true, 'Should return an object');
        $this->assertEquals(is_object($greeting->greeting), true, 'Should return an object called "greeting"');
        $this->assertGreaterThan(0, $greeting->greeting->id, 'Returns a non-numeric id for greeting');
        $this->assertEquals($greeting->greeting->name, 'Hello', 'Name of test greeting does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $greeting->greeting->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreateGreeting
     */
    public function testAllGreeting($stack) {
        $greetings = $this->client->voice()->greetings()->findAll();
        $this->assertEquals(is_object($greetings), true, 'Should return an object');
        $this->assertEquals(is_array($greetings->greetings), true, 'Should return an object containing an array called "greetings"');
        $this->assertGreaterThan(0, $greetings->greetings[0]->id, 'Returns a non-numeric id for greetings[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreateGreeting
     */
    public function testFindGreeting($stack) {
        $id = array_pop($stack);
        $greeting = $this->client->voice()->greeting($id)->find();
        $this->assertGreaterThan(0, $greeting->greeting->id, 'Returns a non-numeric id for greeting');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreateGreeting
     */
    public function testUpdateGreeting(array $stack) {
        $id = array_pop($stack);
        $greeting = $this->client->voice()->greeting($id)->update(array(
            'name' => 'Premium support'
        ));
        $this->assertEquals(is_object($greeting), true, 'Should return an object');
        $this->assertEquals(is_object($greeting->greeting), true, 'Should return an object called "greeting"');
        $this->assertGreaterThan(0, $greeting->greeting->id, 'Returns a non-numeric id for greeting');
        $this->assertEquals($greeting->greeting->name, 'Premium support', 'Name of test greeting does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreateGreeting
     */
    public function testDeleteGreeting(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a greeting id to test with. Did testCreate fail?');
        $result = $this->client->voice()->greeting($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete trigger does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCurrentQueueActivity() {
        $stats = $this->client->voice()->stats()->findAll(array('current_queue_activity' => true));
        $this->assertEquals(is_object($stats), true, 'Should return an object');
        $this->assertEquals(is_object($stats->current_queue_activity), true, 'Should return an object called "current_queue_activity"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testHistoricalQueueActivity() {
        $stats = $this->client->voice()->stats()->findAll(array('historical_queue_activity' => true));
        $this->assertEquals(is_object($stats), true, 'Should return an object');
        $this->assertEquals(is_object($stats->historical_queue_activity), true, 'Should return an object called "historical_queue_activity"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAgentsActivity() {
        $stats = $this->client->voice()->stats()->findAll(array('agents_activity' => true));
        $this->assertEquals(is_object($stats), true, 'Should return an object');
        $this->assertEquals(is_array($stats->agents_activity), true, 'Should return an object containing an array called "agents_activity"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>