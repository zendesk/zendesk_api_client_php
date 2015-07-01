<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

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
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $queryParams = [
            'filter["source_type"]' => 'rule',
            'filter["valid"]'       => 'somerule',
            'invalid'               => 'invalidrule',
        ];

        $this->client->auditLogs()->findAll($queryParams);

        // We expect invalid parameters are removed.
        // We also expect url encoded keys and values
        $expectedQueryParams = [];
        foreach ($queryParams as $key => $value) {
            if ($key == 'invalid') {
                continue;
            }

            $expectedQueryParams = array_merge($expectedQueryParams, [urlencode($key) => urlencode($value)]);
        }

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'audit_logs.json',
                'queryParams' => $expectedQueryParams,
            ]
        );
    }
}
