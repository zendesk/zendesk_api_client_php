<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Audits test class
 */
class TicketFieldsTest extends \PHPUnit_Framework_TestCase {

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
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $fields = $this->client->ticketFields()->findAll();
        $this->assertEquals(is_object($fields), true, 'Should return an object');
        $this->assertEquals(is_array($fields->ticket_fields), true, 'Should return an object containing an array called "ticket_fields"');
        $this->assertGreaterThan(0, $fields->ticket_fields[0]->id, 'Returns a non-numeric id in first ticket field');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $fields = $this->client->ticketField(23153032)->find(); // ticket field #23153032 must never be deleted
        $this->assertEquals(is_object($fields), true, 'Should return an object');
        $this->assertEquals(is_object($fields->ticket_field), true, 'Should return an object called "ticket_field"');
        $this->assertEquals('23153032', $fields->ticket_field->id, 'Returns an incorrect id in ticket field object');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $field = $this->client->ticketFields()->create(array(
            'type' => 'text',
            'title' => 'Age'
        ));
        $this->assertEquals(is_object($field), true, 'Should return an object');
        $this->assertEquals(is_object($field->ticket_field), true, 'Should return an object called "ticket_field"');
        $this->assertGreaterThan(0, $field->ticket_field->id, 'Returns a non-numeric id for ticket_field');
        $this->assertEquals($field->ticket_field->type, 'text', 'Type of test ticket field does not match');
        $this->assertEquals($field->ticket_field->title, 'Age', 'Title of test ticket field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $field->ticket_field->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $field = $this->client->ticketField($id)->update(array(
            'title' => 'Another value'
        ));
        $this->assertEquals(is_object($field), true, 'Should return an object');
        $this->assertEquals(is_object($field->ticket_field), true, 'Should return an object called "ticket_field"');
        $this->assertGreaterThan(0, $field->ticket_field->id, 'Returns a non-numeric id for ticket_field');
        $this->assertEquals($field->ticket_field->type, 'text', 'Type of test ticket field does not match');
        $this->assertEquals($field->ticket_field->title, 'Another value', 'Title of test ticket field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $field->ticket_field->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $field = $this->client->ticketField($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
