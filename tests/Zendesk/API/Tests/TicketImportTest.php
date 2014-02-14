<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Imports test class
 */
class TicketImportTest extends \PHPUnit_Framework_TestCase {

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
    public function testImport() {
        $confirm = $this->client->tickets()->import(array(
            'subject' => 'Help',
            'description' => 'A description',
            'comments' => array(
                array('author_id' => 454094082, 'value' => 'This is a comment') // 454094082 is me
            )
        ));
        $this->assertEquals(is_object($confirm), true, 'Should return an object');
        $this->assertEquals(is_object($confirm->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $confirm->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($confirm->ticket->subject, 'Help', 'Subject of test ticket does not match');
        $this->assertEquals($confirm->ticket->description, 'A description', 'Description of test ticket does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

}

?>
