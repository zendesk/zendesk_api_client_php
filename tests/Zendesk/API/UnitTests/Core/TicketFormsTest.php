<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Ticket Audits test class
 */
class TicketFormsTest extends BasicTest
{
    /**
     * @expectedException Zendesk\API\Exceptions\ApiResponseException
     */
    public function testDeleteThrowsException()
    {
        $this->mockAPIResponses([
            new Response(422, [], '')
        ]);

        $this->client->tickets()->forms(1)->delete();

        $this->assertLastRequestIs(
            [
                'method'   => 'DELETE',
                'endpoint' => 'ticket_forms/1.json'
            ]
        );
    }

    public function testCloneForm()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->forms(1)->cloneForm();

        $this->assertLastRequestIs(
            [
                'method'   => 'POST',
                'endpoint' => 'ticket_forms/1/clone.json'
            ]
        );
    }

    /**
     * Tests if an exception is thrown when a ticket form ID could not be retrieved from
     * the method call.
     *
     * @expectedException Zendesk\API\Exceptions\MissingParametersException
     */
    public function testCloneFormThrowsException()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets(1)->forms()->cloneForm();

        $this->assertLastRequestIs(
            [
                'method'   => 'POST',
                'endpoint' => 'ticket_forms/1/clone.json'
            ]
        );
    }

    public function testReorder()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->forms()->reorder([3, 4, 5, 1]);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'ticket_forms/reorder.json',
                'postFields' => ['ticket_form_ids' => [3, 4, 5, 1]]
            ]
        );
    }
}
