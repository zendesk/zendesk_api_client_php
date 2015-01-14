<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * AuditLogs test class
 */
class AuditLogsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }
    protected $ticket;
    
    public function setUp(){
        $testTicket = array(
            'subject' => 'The is for Audit Logs test', 
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ), 
            'priority' => 'normal'
        );
        $this->ticket = $this->client->tickets()->create($testTicket);
    }

    public function tearDown(){
        $this->client->tickets($this->ticket->ticket->id)->delete();
    }
    public function testAll() {
        $auditLogs = $this->client->auditLogs()->findAll(array(
            'filter' => array(
                'source_type' => 'rule'
            )
        ));
        $this->assertEquals(is_object($auditLogs), true, 'Should return an object');
        $this->assertEquals(is_array($auditLogs->audit_logs), true, 'Should return an object containing an array called "audit_logs"');
        $this->assertGreaterThan(0, $auditLogs->audit_logs[0]->id, 'Returns a non-numeric id for audit_logs[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $auditLog_id = $this->client->auditLogs()->findAll()->audit_logs[0]->id;
        $auditLog = $this->client->auditLog($auditLog_id)->find(); // don't delete audit log #24000361
        $this->assertEquals(is_object($auditLog), true, 'Should return an object');
        $this->assertGreaterThan(0, $auditLog->audit_log->id, 'Returns a non-numeric id for audit_log');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
