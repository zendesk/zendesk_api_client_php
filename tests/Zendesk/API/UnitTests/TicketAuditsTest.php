<?php

namespace Zendesk\API\UnitTests;

/**
 * Ticket Audits test class
 */
class TicketAuditsTest extends BasicTest
{
    protected $ticket_id = 12345;

    public function testFindAllWithChainedParams()
    {
        $this->mockApiCall(
          'GET',
          'tickets/12345/audits.json',
          [
            'audits' => [
              [
                'id' => '1'
              ]
            ]
          ]
        );

        $this->client->tickets($this->ticket_id)->audits()->findAll();
        $this->httpMock->verify();
    }

    public function testFindWithChainedParams()
    {
        $audit_id = 1;

        $this->mockApiCall(
          'GET',
          'tickets/12345/audits/1.json',
          [
            'audit' => [
              'id' => '1'
            ]
          ]
        );
        $audits = $this->client->tickets($this->ticket_id)->audits($audit_id)->find();
        $this->httpMock->verify();

        $this->assertEquals(is_object($audits->audit), true,
          'Should return an object containing an array called "audit"');
        $this->assertEquals($audit_id, $audits->audit->id, 'Returns an incorrect id in audit object');
    }

}
