<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Ticket Imports test class
 */
class TicketImportTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    public function testImport()
    {
        /*
         * Create the user first, and we'll delete it later
         */
        $user = $this->client->users()->create(array(
            'name' => 'Roger Wilco',
            'email' => 'roge@example.org',
            'role' => 'agent',
            'verified' => true
        ));
        $author_id = $user->user->id;

        $confirm = $this->client->tickets()->import(array(
            'subject' => 'Help',
            'description' => 'A description',
            'comments' => array(
                array('author_id' => $author_id, 'value' => 'This is a author comment') // 454094082 is me
            )
        ));
        $this->assertEquals(is_object($confirm), true, 'Should return an object');
        $this->assertEquals(is_object($confirm->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $confirm->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($confirm->ticket->subject, 'Help', 'Subject of test ticket does not match');
        $this->assertEquals($confirm->ticket->description, 'A description',
            'Description of test ticket does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');

        $this->client->user($author_id)->delete();
    }

}
