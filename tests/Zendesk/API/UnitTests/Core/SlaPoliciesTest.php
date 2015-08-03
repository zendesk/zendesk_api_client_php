<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\Resources\Core\SlaPolicies;
use Zendesk\API\UnitTests\BasicTest;

/**
 * SLA Policies test class
 */
class SlaPoliciesTest extends BasicTest
{
    /**
     * Test that the default methods were included
     */
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->slaPolicies(), 'findAll'));
        $this->assertTrue(method_exists($this->client->slaPolicies(), 'create'));
        $this->assertTrue(method_exists($this->client->slaPolicies(), 'find'));
        $this->assertTrue(method_exists($this->client->slaPolicies(), 'delete'));
        $this->assertTrue(method_exists($this->client->slaPolicies(), 'update'));
    }

    /**
     * Test that the resource was overridden
     */
    public function testResourceName()
    {
        $this->assertEquals('slas/policies', $this->client->slaPolicies()->getResourceName());
    }

    /**
     * Test the replace method
     */
    public function testReplace()
    {
        $resourceId   = 91918;
        $updateFields = [
            'title'          => 'Incidents',
            'description'    => 'For urgent incidents, we will respond to tickets in 10 minutes',
            'position'       => 3,
            'filter'         =>
                [
                    'all' =>
                        [
                            0 =>
                                [
                                    'field'    => 'type',
                                    'operator' => 'is',
                                    'value'    => 'incident',
                                ],
                        ],
                        'any' =>
                        [
                        ],
                ],
                'policy_metrics' =>
                [
                    0 =>
                        [
                            'priority'       => 'normal',
                            'metric'         => 'first_reply_time',
                            'target'         => 30,
                            'business_hours' => false,
                        ],
                        1 =>
                        [
                            'priority'       => 'urgent',
                            'metric'         => 'first_reply_time',
                            'target'         => 10,
                            'business_hours' => false,
                        ],
                ],
        ];

        $this->assertEndpointCalled(
            function () use ($resourceId, $updateFields) {
                $this->client->slaPolicies()->replace($resourceId, $updateFields);
            },
            "slas/policies/{$resourceId}/replace.json",
            'PUT',
            ['postFields' => ['sla_policy' => $updateFields]]
        );
    }

    /**
     * Test the reorder method
     */
    public function testReorder()
    {
        $resourceIds = [12, 55];
        $this->assertEndpointCalled(
            function () use ($resourceIds) {
                $this->client->slaPolicies()->reorder($resourceIds);
            },
            "slas/policies/reorder.json",
            'PUT',
            ['postFields' => ['sla_policy_ids' => $resourceIds]]
        );
    }

    /**
     * Test the definitions method
     */
    public function testDefinitions()
    {
        $this->assertEndpointCalled(
            function () {
                $this->client->slaPolicies()->definitions();
            },
            "slas/policies/definitions.json",
            'GET'
        );
    }
}
