<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * VoiceIntegration test class
 */
class VoiceIntegrationTest extends \PHPUnit_Framework_TestCase {

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
    public function testOpenUserProfile() {
        $result = $this->client->voice()->agents()->openUserProfile(array(
            'agent_id' => '1',
            'user_id' => '1'
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testOpenTicket() {
        $result = $this->client->voice()->agents()->openTicket(array(
            'agent_id' => '1',
            'ticket_id' => '1'
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreateVoiceTicket() {
        $ticket = $this->client->voice()->tickets()->create(array(
            'display_to_agent' => '1',
            'ticket' => array(
                'via_id' => 44,
                'subject' => 'My printer is on fire!',
                'comment' => array(
                    'body' => 'The smoke is very colorful'
                ),
                'priority' => 'urgent'
            )
        ));
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($ticket->ticket->subject, 'My printer is on fire!', 'Subject of test ticket does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreateVoicemailTicket() {
        $ticket = $this->client->voice()->tickets()->create(array(
            'ticket' => array(
                'via_id' => 45,
                'description' => 'Incoming phone call from: +16617480240',
                'voice_comment' => array(
                    'from' => '+16617480240',
                    'to' => '+16617480123',
                    'recording_url' => 'http://yourdomain.com/recordings/1.mp3',
                    'started_at' => '2013-07-11 15:31:44 +0000',
                    'call_duration' => 40,
                    'answered_by_id' => 28,
                    'transcription_text' => 'The transcription of the call',
                    'location' => 'Dublin, Ireland'
                )
            )
        ));
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($ticket->ticket->description, 'Incoming phone call from: +16617480240', 'Description of test voicemail does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

}

?>
