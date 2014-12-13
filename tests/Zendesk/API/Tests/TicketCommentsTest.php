<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Comments test class
 */
class TicketCommentsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
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
