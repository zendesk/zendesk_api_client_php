<?php

namespace Zendesk\API\LiveTests;

/**
 * Ticket Audits test class
 */
class TicketAuditsTest extends BasicTest
{
    protected $ticket_id;

    public function setUp()
    {
        $testTicket = array(
            'id' => '12345',
            'subject' => 'The quick brown fox jumps over the lazy dog',
            'comment' => array(
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );

        $this->ticket_id = $testTicket['id'];

        parent::setUp();
    }

    public function testAll()
    {
        $this->mockApiCall('GET', '/tickets/12345/audits.json?',
          array(
            'audits' => array(
                array(
                    'id' => '1'
                )
            )
          )
        );

        $audits = $this->client->ticket($this->ticket_id)->audits()->findAll();
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_array($audits->audits), true,
            'Should return an object containing an array called "audits"');
        $this->assertGreaterThan(0, $audits->audits[0]->id, 'Returns a non-numeric id in first audit');
    }

    public function testAllSideLoadedMethod()
    {
        $this->mockApiCall('GET', '/tickets/12345/audits.json?include=users%2Cgroups&',
          array(
            'audits' => array(),
            'users' => array(),
            'groups' => array(),
          )
        );

        $audits = $this->client->ticket($this->ticket_id)->sideload(array('users', 'groups'))->audits()->findAll();
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_array($audits->users), true,
            'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($audits->groups), true,
            'Should return an object containing an array called "groups"');
    }

    public function testAllSideLoadedParameter()
    {
        $this->mockApiCall('GET', '/tickets/12345/audits.json?include=users%2Cgroups&',
          array(
            'audits' => array(),
            'users' => array(),
            'groups' => array(),
          )
        );

        $audits = $this->client->ticket($this->ticket_id)->audits()->findAll(array(
            'sideload' => array(
                'users',
                'groups'
            )
        ));
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_array($audits->users), true,
            'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($audits->groups), true,
            'Should return an object containing an array called "groups"');
    }

    public function testFind()
    {
        $this->mockApiCall('GET', '/tickets/12345/audits.json?',
          array(
            'audits' => array(
              array(
                'id' => '1'
              )
            )
          )
        );
        $audit_id = $this->client->ticket($this->ticket_id)->audits()->findAll()->audits[0]->id;

        $this->mockApiCall('GET', '/tickets/12345/audits/1.json?',
          array(
            'audit' => array(
              'id' => '1'
            )
          )
        );
        $audits = $this->client->ticket($this->ticket_id)->audit($audit_id)->find();
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_object($audits->audit), true,
            'Should return an object containing an array called "audit"');
        $this->assertEquals($audit_id, $audits->audit->id, 'Returns an incorrect id in audit object');
    }

    /*
     * Test mark as trusted. Need a voice comment or Facebook comment for this test
     */
    // public function testMarkAsTrusted() {
    //     $audits = $this->client->ticket(2)->audit(16317679361)->markAsTrusted();
    //     $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    // }

}
