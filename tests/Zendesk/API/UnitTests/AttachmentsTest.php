<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Attachments test class
 */
class AttachmentsTest extends BasicTest
{

    /**
     * Test upload of file
     */
    public function testUploadAttachment()
    {
        $this->mockAPIResponses([
            new Response(201, [], '')
        ]);

        $attachmentData = [
            'file' => getcwd() . '/tests/assets/UK.png',
            'type' => 'image/png',
            'name' => 'UK test non-alpha chars.png'
        ];

        $this->client->attachments()->upload($attachmentData);

        $this->assertLastRequestIs(
            [
                'method'      => 'POST',
                'endpoint'    => 'uploads.json',
                'statusCode'  => 201,
                'queryParams' => ['filename' => rawurlencode($attachmentData['name'])],
                'file'        => $attachmentData['file'],
            ]
        );
    }

    /**
     * Test upload of file
     */
    public function testUploadAttachmentWithNoName()
    {
        $this->mockAPIResponses([
            new Response(201, [], '')
        ]);

        $attachmentData = [
            'file' => getcwd() . '/tests/assets/UK.png',
            'type' => 'image/png',
        ];

        $this->client->attachments()->upload($attachmentData);

        $this->assertLastRequestIs(
            [
                'method'      => 'POST',
                'endpoint'    => 'uploads.json',
                'statusCode'  => 201,
                'queryParams' => ['filename' => 'UK.png'], // Taken from file path
                'file'        => $attachmentData['file'],
            ]
        );
    }

    /**
     * Test delete uploaded file
     */
    public function testDeleteAttachment()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $token = 'validToken';

        $this->client->attachments()->deleteUpload($token);

        $this->assertLastRequestIs(
            [
                'method'   => 'DELETE',
                'endpoint' => "uploads/{$token}.json",
            ]
        );
    }

    /**
     * Test routes for find and delete are present
     */
    public function testRoutes()
    {
        $attachment = $this->client->attachments();

        // Test route for find
        $id = 1;
        $this->assertEquals("attachments/{$id}.json", $attachment->getRoute('find', ['id' => $id]));

        // Test route for delete
        $this->assertEquals("attachments/{$id}.json", $attachment->getRoute('delete', ['id' => $id]));
    }
}
