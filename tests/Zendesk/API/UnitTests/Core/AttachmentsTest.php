<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\LazyOpenStream;
use Zendesk\API\UnitTests\BasicTest;

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
        $attachmentData = [
            'file' => getcwd() . '/tests/assets/UK.png',
            'type' => 'image/png',
            'name' => 'UK test non-alpha chars.png'
        ];

        $this->assertEndpointCalled(
            function () use ($attachmentData) {
                $this->client->attachments()->upload($attachmentData);
            },
            'uploads.json',
            'POST',
            [
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
        $attachmentData = [
            'file' => getcwd() . '/tests/assets/UK.png',
            'type' => 'image/png',
        ];

        $this->assertEndpointCalled(
            function () use ($attachmentData) {
                $this->client->attachments()->upload($attachmentData);
            },
            'uploads.json',
            'POST',
            [
                'queryParams' => ['filename' => 'UK.png'], // Taken from file path
                'file'        => $attachmentData['file'],
            ]
        );
    }

    /**
     * Test upload of file stream
     */
    public function testUploadAttachmentStream()
    {
        $attachmentData = [
            'file' => new LazyOpenStream(getcwd() . '/tests/assets/UK.png', 'r'),
            'type' => 'image/png',
            'name' => 'UK test non-alpha chars.png'
        ];

        $this->assertEndpointCalled(
            function () use ($attachmentData) {
                $this->client->attachments()->upload($attachmentData);
            },
            'uploads.json',
            'POST',
            [
                'queryParams' => ['filename' => rawurlencode($attachmentData['name'])],
                'file'        => $attachmentData['file'],
            ]
        );
    }

    /**
     * Test upload of file stream with no name
     */
    public function testUploadAttachmentStreamWithNoName()
    {
        $attachmentData = [
            'file' => new LazyOpenStream(getcwd() . '/tests/assets/UK.png', 'r'),
            'type' => 'image/png',
        ];

        $this->assertEndpointCalled(
            function () use ($attachmentData) {
                $this->client->attachments()->upload($attachmentData);
            },
            'uploads.json',
            'POST',
            [
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
        $token = 'validToken';

        $this->assertEndpointCalled(function () use ($token) {
            $this->client->attachments()->deleteUpload($token);
        }, "uploads/{$token}.json", 'DELETE');
    }
}
