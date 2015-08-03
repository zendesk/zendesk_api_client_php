<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Attachments test class
 */
class AppsTests extends BasicTest
{

    /**
     * Test uploading of App
     */
    public function testUploadApp()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = ['file' => getcwd() . '/tests/assets/app.zip'];

        $this->client->apps()->upload($postFields);

        $this->assertLastRequestIs(
            [
                'method'    => 'POST',
                'endpoint'  => 'apps/uploads.json',
                'multipart' => true,
            ]
        );
    }

    /**
     * Test creating of app from upload
     */
    public function testCreateApp()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $uploadId   = 'diid22';
        $postFields = [
            'name'              => 'TESTING APP' . rand(),
            'short_description' => 'testing',
            'upload_id'         => (string) $uploadId
        ];

        $this->client->apps()->create($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'apps.json',
                'postFields' => $postFields,
            ]
        );
    }

    /**
     * Test getting of the upload status.
     */
    public function testJobStatusApp()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $resourceId = 'asdfg';

        $this->client->apps()->jobStatus(['id' => $resourceId]);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "apps/job_statuses/{$resourceId}.json",
            ]
        );
    }

    /**
     * Test updating of app
     */
    public function testUpdateApp()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $resourceId = 82827;

        $putData = [
            'name'              => 'updated Name',
            'short_description' => 'update short desription',
            'upload_id'         => '263858'
        ];

        $this->client->apps()->update($resourceId, $putData);

        $this->assertLastRequestIs(
            [
                'method'   => 'PUT',
                'endpoint' => "apps/{$resourceId}.json",
                'postData' => $putData,
            ]
        );
    }

    /**
     * Test finding all of the user's owned apps.
     */
    public function testFindAllOwned()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->apps()->findAllOwned();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'apps/owned.json',
            ]
        );
    }

    /**
     * Test endpoint to notify app users
     */
    public function testNotify()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = [
            'app_id'   => 375,
            'event'    => 'updateUsersPhoneNumber',
            'body'     => '61455534512',
            'agent_id' => 534,
        ];

        $this->client->apps()->notify($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'apps/notify.json',
                'postFields' => $postFields,
            ]
        );
    }

    /**
     * Test install an app
     */
    public function testInstall()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = [
            'app_id'   => '225',
            'settings' =>
                [
                    'name'      => 'Helpful App',
                    'api_token' => '53xjt93n6tn4321p',
                    'use_ssl'   => true,
                ],
        ];

        $this->client->apps()->install($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'apps/installations.json',
                'postFields' => $postFields,
            ]
        );
    }
}
