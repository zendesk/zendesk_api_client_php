<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * AuditLogs test class
 */
class AuditLogsTest extends BasicTest
{
    /**
     * Test find all method
     */
    public function testFindAll()
    {
        $queryParams = [
            'sort_by'              => 'actor_id',
            'sort_order'           => 'desc',
            'filter[source_type]'  => 'rule',
            'filter[valid]'        => 'somerule',
            'filter[created_at][]' => '2016-01-01T00:00:00Z'
        ];

        // We expect invalid parameters are removed.
        // We also expect url encoded keys and values
        $expectedQueryParams = [];
        foreach ($queryParams as $key => $value) {
            $expectedQueryParams = array_merge($expectedQueryParams, [urlencode($key) => $value]);
        }

        $this->assertEndpointCalled(
            function () use ($queryParams) {
                $this->client->auditLogs()->findAll($queryParams);
            },
            'audit_logs.json',
            'GET',
            ['queryParams' => $expectedQueryParams]
        );
    }
}
