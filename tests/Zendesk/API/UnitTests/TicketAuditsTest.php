<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Ticket Audits test class
 */
class TicketAuditsTest extends BasicTest
{
    protected $ticket_id = 12345;

    public function testFindAllWithChainedParams()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets($this->ticket_id)->audits()->findAll(['per_page' => 1]);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'tickets/12345/audits.json',
                'queryParams' => ['per_page' => 1]
            ]
        );
    }

    public function testFindWithChainedParams()
    {
        $audit_id = 1;

        $response = [
            'audit' => [
                'id' => '1'
            ]
        ];
        $this->mockAPIResponses([
            new Response(200, [], json_encode($response))
        ]);

        $audits = $this->client->tickets($this->ticket_id)->audits($audit_id)->find();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'tickets/12345/audits/1.json',
            ]
        );

        $this->assertEquals(
            is_object($audits->audit),
            true,
            'Should return an object containing an array called "audit"'
        );
        $this->assertEquals($audit_id, $audits->audit->id, 'Returns an incorrect id in audit object');
    }
}
