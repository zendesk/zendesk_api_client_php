<?php
// FINISH THIS!

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Tags test class
 */
class TagsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    protected $ticket_id;
    public function setUp() {
        /*
         * First start by creating a topic (we'll delete it later)
         */
        $testTicket = array(
            'subject' => 'This is for tag test', 
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ), 
            'priority' => 'normal'
        );
        $ticket = $this->client->tickets()->create($testTicket);
        $this->ticket_id = $ticket->ticket->id;
        /*
         * Continue with the rest of the test...
         */
        $tags = $this->client->ticket($this->ticket_id)->tags()->create(array('tags' => array('important')));
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('important', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

    public function testAll() {
        $tags = $this->client->tags()->findAll();
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate() {
        $tags = $this->client->ticket($this->ticket_id)->tags()->update(array('tags' => array('customer')));
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('customer', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $tags = $this->client->ticket($this->ticket_id)->tags()->find();
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('important', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown() {
        $tags = $this->client->ticket($this->ticket_id)->tags()->delete(array('tags' => 'customer'));
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an array called "tags"');
        $this->assertEquals(in_array('important', $tags->tags), true, 'Added tag does not exist');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
         /*
         * Clean-up
         */
        $this->client->ticket($this->ticket_id)->delete();
    }

}

?>
