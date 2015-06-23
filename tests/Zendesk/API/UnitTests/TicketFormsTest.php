<?php

namespace Zendesk\API\UnitTests;

/**
 * Ticket Audits test class
 */
class TicketFormsTest extends BasicTest
{
    /**
     * @expectedException Zendesk\API\Exceptions\ResponseException
     */
    public function testDeleteThrowsException()
    {
        $this->mockApiCall('DELETE', 'ticket_forms/1.json', [], ['statusCode' => 422]);
        $this->client->tickets()->forms(1)->delete();
    }

    public function testCloneForm()
    {
        $this->mockApiCall('POST', 'ticket_forms/1/clone.json', [], ['statusCode' => 200]);
        $this->client->tickets()->forms(1)->cloneForm();
        $this->httpMock->verify();
    }

    /**
     * Tests if an exception is thrown when a ticket form ID could not be retrieved from
     * the method call.
     *
     * @expectedException Zendesk\API\Exceptions\MissingParametersException
     */
    public function testCloneFormThrowsException()
    {
        $this->mockApiCall('POST', 'ticket_forms/1/clone.json', [], ['statusCode' => 200]);
        $this->client->tickets(1)->forms()->cloneForm();
        $this->httpMock->verify();
    }

    public function testReorder()
    {
        $this->mockApiCall('PUT', 'ticket_forms/reorder.json', [], [
                'statusCode' => 200,
                'bodyParams' => ['ticket_form_ids' => [3, 4, 5, 1]]
            ]);
        $this->client->tickets()->forms()->reorder([3, 4, 5, 1]);
        $this->httpMock->verify();
    }
}
