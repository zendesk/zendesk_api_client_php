<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Ticket Audits test class
 */
class TicketAuditsTest extends BasicTest
{
    /**
     * @var int
     */
    protected $ticketId = 12345;

    /**
     * Test findAll with chained resources
     */
    public function testFindAllWithChainedParams()
    {
        $queryParams = ['per_page' => 1];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->tickets($this->ticketId)->audits()->findAll($queryParams);
        }, "tickets/{$this->ticketId}/audits.json", 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Test find with chained resources
     */
    public function testFindWithChainedParams()
    {
        $auditId = 1;

        $this->assertEndpointCalled(function () use ($auditId) {
            $this->client->tickets($this->ticketId)->audits($auditId)->find();
        }, "tickets/{$this->ticketId}/audits/{$auditId}.json");
    }
}
