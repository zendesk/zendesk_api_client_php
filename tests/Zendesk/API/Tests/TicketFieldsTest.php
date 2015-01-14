<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Audits test class
 */
class TicketFieldsTest extends BasicTest {
    
    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    protected $id;

    public function setUp() {
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
        $this->id = $field->ticket_field->id;
    }

    public function testAll() {
        $fields = $this->client->ticketFields()->findAll();
        $this->assertEquals(is_object($fields), true, 'Should return an object');
        $this->assertEquals(is_array($fields->ticket_fields), true, 'Should return an object containing an array called "ticket_fields"');
        $this->assertGreaterThan(0, $fields->ticket_fields[0]->id, 'Returns a non-numeric id in first ticket field');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $fields = $this->client->ticketField($this->id)->find(); 
        $this->assertEquals(is_object($fields), true, 'Should return an object');
        $this->assertEquals(is_object($fields->ticket_field), true, 'Should return an object called "ticket_field"');
        $this->assertEquals($this->id, $fields->ticket_field->id, 'Returns an incorrect id in ticket field object');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate() {
        $field = $this->client->ticketField($this->id)->update(array(
            'title' => 'Another value'
        ));
        $this->assertEquals(is_object($field), true, 'Should return an object');
        $this->assertEquals(is_object($field->ticket_field), true, 'Should return an object called "ticket_field"');
        $this->assertGreaterThan(0, $field->ticket_field->id, 'Returns a non-numeric id for ticket_field');
        $this->assertEquals($field->ticket_field->type, 'text', 'Type of test ticket field does not match');
        $this->assertEquals($field->ticket_field->title, 'Another value', 'Title of test ticket field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown() {
        $field = $this->client->ticketField($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }
    
}

?>
