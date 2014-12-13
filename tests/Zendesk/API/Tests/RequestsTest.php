<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Requests test class
 */
class RequestsTest extends BasicTest {

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
        $requests = $this->client->requests()->findAll();
        $this->assertEquals(is_object($requests), true, 'Should return an object');
        $this->assertEquals(is_array($requests->requests), true, 'Should return an object containing an array called "requests"');
        $this->assertGreaterThan(0, $requests->requests[0]->id, 'Returns a non-numeric id for requests[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $request = $this->client->request(218)->find();
        $this->assertEquals(is_object($request), true, 'Should return an object');
        $this->assertEquals(is_object($request->request), true, 'Should return an object called "request"');
        $this->assertGreaterThan(0, $request->request->id, 'Returns a non-numeric id for request');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $request = $this->client->requests()->create(array(
            'subject' => 'Help!',
            'comment' => array(
                'body' => 'My printer is on fire!'
            )
        ));
        $this->assertEquals(is_object($request), true, 'Should return an object');
        $this->assertEquals(is_object($request->request), true, 'Should return an object called "request"');
        $this->assertGreaterThan(0, $request->request->id, 'Returns a non-numeric id for request');
        $this->assertEquals($request->request->subject, 'Help!', 'Subject of test request does not match');
        $this->assertEquals($request->request->description, 'My printer is on fire!', 'Description of test request does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $request->request->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $request = $this->client->request($id)->update(array(
            'comment' => array(
                'body' => 'Thanks!'
            )
        ));
        $this->assertEquals(is_object($request), true, 'Should return an object');
        $this->assertEquals(is_object($request->request), true, 'Should return an object called "request"');
        $this->assertGreaterThan(0, $request->request->id, 'Returns a non-numeric id for request');
        $this->assertEquals($request->request->subject, 'Help!', 'Name of test request does not match');
        $this->assertEquals($request->request->description, 'Thanks!', 'Description of test request does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $request->request->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testAuthToken
     */
    public function testComments() {
        $comments = $this->client->request(218)->comments()->findAll();
        $this->assertEquals(is_object($comments), true, 'Should return an object');
        $this->assertEquals(is_array($comments->comments), true, 'Should return an object containing an array called "comments"');
        $this->assertGreaterThan(0, $comments->comments[0]->id, 'Returns a non-numeric id for comments[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFindComment() {
        $comment = $this->client->request(218)->comment(456)->find();
        $this->assertEquals(is_object($comment), true, 'Should return an object');
        $this->assertEquals(is_array($comment->comment), true, 'Should return an object containing an array called "comment"');
        $this->assertGreaterThan(0, $comment->comment->id, 'Returns a non-numeric id for comment');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
