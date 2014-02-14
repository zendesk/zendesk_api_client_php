<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Audits test class
 */
class TicketFormsTest extends \PHPUnit_Framework_TestCase {

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
        $forms = $this->client->ticketForms()->findAll();
        $this->assertEquals(is_object($forms), true, 'Should return an object');
        $this->assertEquals(is_array($forms->ticket_forms), true, 'Should return an object containing an array called "ticket_forms"');
        $this->assertGreaterThan(0, $forms->ticket_forms[0]->id, 'Returns a non-numeric id in first ticket form');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $forms = $this->client->ticketForm(9881)->find(); // ticket form #9881 must never be deleted
        $this->assertEquals(is_object($forms), true, 'Should return an object');
        $this->assertEquals(is_object($forms->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertEquals('9881', $forms->ticket_form->id, 'Returns an incorrect id in ticket form object');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $form = $this->client->ticketForms()->create(array(
            'name' => 'Snowboard Problem',
            'end_user_visible' => true,
            'display_name' => 'Snowboard Damage',
            'position' => 2,
            'active' => true,
            'default' => false
        ));
        $this->assertEquals(is_object($form), true, 'Should return an object');
        $this->assertEquals(is_object($form->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertGreaterThan(0, $form->ticket_form->id, 'Returns a non-numeric id for ticket_form');
        $this->assertEquals($form->ticket_form->name, 'Snowboard Problem', 'Name of test ticket form does not match');
        $this->assertEquals($form->ticket_form->display_name, 'Snowboard Damage', 'Display name of test ticket form does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $form->ticket_form->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $form = $this->client->ticketForm($id)->update(array(
            'name' => 'Snowboard Fixed',
            'display_name' => 'Snowboard has been fixed'
        ));
        $this->assertEquals(is_object($form), true, 'Should return an object');
        $this->assertEquals(is_object($form->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertGreaterThan(0, $form->ticket_form->id, 'Returns a non-numeric id for ticket_form');
        $this->assertEquals($form->ticket_form->name, 'Snowboard Fixed', 'Name of test ticket form does not match');
        $this->assertEquals($form->ticket_form->display_name, 'Snowboard has been fixed', 'Display name of test ticket form does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $form->ticket_form->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        /*
         * First deactivate, then delete
         */
        $response = $this->client->ticketForm($id)->deactivate();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Deactivate does not return HTTP code 200');
        $form = $this->client->ticketForm($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testReorder(array $stack) {
        $allForms = $this->client->ticketForms()->findAll();
        $allIds = array();
        while(list($k, $form) = each($allForms->ticket_forms)) {
            $allIds[] = $form->id;
        }
        array_unshift($allIds, array_pop($allIds));
        $form = $this->client->ticketForms()->reorder($allIds);
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testClone(array $stack) {
        $id = array_pop($stack);
        $form = $this->client->ticketForm(10782)->cloneForm(); // don't delete ticket form id #10782
        $this->assertEquals(is_object($form), true, 'Should return an object');
        $this->assertEquals(is_object($form->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertGreaterThan(0, $form->ticket_form->id, 'Returns a non-numeric id for ticket_form');
        $this->assertEquals($form->ticket_form->name, 'Snowboard Fixed', 'Name of test ticket form does not match');
        $this->assertEquals($form->ticket_form->display_name, 'Snowboard has been fixed', 'Display name of test ticket form does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $form->ticket_form->id;
        $response = $this->client->ticketForm($id)->deactivate();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Deactivate does not return HTTP code 200');
        $form = $this->client->ticketForm($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete does not return HTTP code 200');
    }

}

?>
