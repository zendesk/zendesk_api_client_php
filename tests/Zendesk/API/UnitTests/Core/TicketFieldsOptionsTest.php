<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\Resources\Core\TicketFieldsOptions;
use Zendesk\API\UnitTests\BasicTest;

/**
 * TicketFieldsOptionsTest test class
 */
class TicketFieldsOptionsTest extends BasicTest
{

    /**
     * Tests if the unique routes are called correctly
     */
    public function testRoutes()
    {
        $fieldId      = 3124;
        $id           = 123;
        $optionValues = [
            'name' => 'one more',
            'value'=> 'ça bouge',
        ];

        // FindAll
        $this->assertEquals(
            "ticket_fields/{$fieldId}/options.json",
            $this->client->ticketFields($fieldId)->options()->getRoute(
                'findAll',
                ['fieldId' => $fieldId]
            )
        );

        // Create
        $this->assertEquals(
            "ticket_fields/{$fieldId}/options.json",
            $this->client->ticketFields($fieldId)->options()->getRoute(
                'create',
                ['fieldId' => $fieldId]
            )
        );

        // Find
        $this->assertEquals(
            "ticket_fields/{$fieldId}/options/{$id}.json",
            $this->client->ticketFields($fieldId)->options($id)->getRoute(
                'find',
                ['id' => $id, 'fieldId' => $fieldId]
            )
        );

        // Delete
        $this->assertEquals(
            "ticket_fields/{$fieldId}/options/{$id}.json",
            $this->client->ticketFields($fieldId)->options($id)->getRoute(
                'delete',
                ['id' => $id, 'fieldId' => $fieldId]
            )
        );

        // Update
        $this->assertEquals(
            "ticket_fields/{$fieldId}/options.json",
            $this->client->ticketFields($fieldId)->options($id, $optionValues)->getRoute(
                'update',
                ['id' => $id, 'fieldId' => $fieldId]
            )
        );
    }
}
