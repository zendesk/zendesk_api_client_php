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
            'filter[source_type]' => 'rule',
            'filter[valid]'       => 'somerule',
        ];

        // We expect invalid parameters are removed.
        // We also expect url encoded keys and values
        $expectedQueryParams = [];
        foreach ($queryParams as $key => $value) {
            $expectedQueryParams = array_merge($expectedQueryParams, [urlencode($key) => urlencode($value)]);
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
