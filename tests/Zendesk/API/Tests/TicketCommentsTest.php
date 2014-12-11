<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Comments test class
 */
class TicketCommentsTest extends BasicTest {

    public function testCredentials() {
        $this->assertEquals($_ENV['SUBDOMAIN'] != '', true, 'Expecting _ENV[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['TOKEN'] != '', true, 'Expecting _ENV[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['USERNAME'] != '', true, 'Expecting _ENV[USERNAME] parameter; does phpunit.xml exist?');
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
        $comments = $this->client->ticket(76)->comments()->findAll(); // Don't delete ticket #76
        $this->assertEquals(is_object($comments), true, 'Should return an object');
        $this->assertEquals(is_array($comments->comments), true, 'Should return an object containing an array called "comments"');
        $this->assertGreaterThan(0, $comments->comments[0]->id, 'Returns a non-numeric id in first audit');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /*
     * Test make private
     */
    public function testMakePrivate() {
        $this->markTestSkipped(
            'Skipped for now because it requires a new (unique) comment id for each test'
        );
        $comments = $this->client->ticket(76)->comments(16303442242)->makePrivate();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
