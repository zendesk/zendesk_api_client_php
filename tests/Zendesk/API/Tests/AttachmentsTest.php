<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Attachments test class
 */
class AttachmentsTest extends BasicTest {

    public function testAuthToken() {
        parent::authTokenTest();
    }

    public function testUploadAttachment() {
        $attachment = $this->client->attachments()->upload(array(
            'file' => getcwd().'/tests/assets/UK.png',
            'type' => 'image/png',
            'name' => 'UK test non-alpha chars.png'
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->assertEquals(is_object($attachment), true, 'Should return an object');
        $this->assertEquals(is_object($attachment->upload), true, 'Should return an object called "upload"');
        $this->assertEquals(($attachment->upload->token != ''), true, 'Should return a token');
        $this->assertEquals(is_array($attachment->upload->attachments), true, 'Should return an array called "upload->attachments"');
        $this->assertGreaterThan(0, $attachment->upload->attachments[0]->id, 'Returns a non-numeric id for upload->attachments[0]');
        $this->assertGreaterThan(0, $attachment->upload->attachments[0]->size, 'returns a file with a greater than nothing filesize');
        $stack = array($attachment);
        return $stack;
    }

    /**
     * @depends testUploadAttachment
     */
    public function testDeleteAttachment(array $stack) {
        $attachment = array_pop($stack);
        $this->assertEquals(($attachment->upload->token != ''), true, 'Cannot find a token to test with. Did testUploadAttachment fail?');
        $confirmed = $this->client->attachments()->delete(array(
            'token' => $attachment->upload->token
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUploadAttachmentBody() {
        $body = file_get_contents(getcwd().'/tests/assets/UK.png');
        $attachment = $this->client->attachments()->uploadWithBody(array(
            'body' => $body,
            'type' => 'image/png',
            'name' => 'UK.png'
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->assertEquals(is_object($attachment), true, 'Should return an object');
        $this->assertEquals(is_object($attachment->upload), true, 'Should return an object called "upload"');
        $this->assertEquals(($attachment->upload->token != ''), true, 'Should return a token');
        $this->assertEquals(is_array($attachment->upload->attachments), true, 'Should return an array called "upload->attachments"');
        $this->assertGreaterThan(0, $attachment->upload->attachments[0]->id, 'Returns a non-numeric id for upload->attachments[0]');
        $this->assertEquals(strlen($body), $attachment->upload->attachments[0]->size, 'returns a file with correct filesize');
        $stack = array($attachment);
        return $stack;
    }

}

?>
