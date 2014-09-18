<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * AuditLogs test class
 */
class AuditLogsTest extends \PHPUnit_Framework_TestCase {

    private $client;
    private $subdomain;
    private $username;
    private $password;
    private $token;
    private $oAuthToken;

    public function __construct() {
        $this->subdomain = $GLOBALS['SUBDOMAIN'];
        $this->username = $GLOBALS['USERNAME'];
        $this->password = $GLOBALS['PASSWORD'];
        $this->token = $GLOBALS['TOKEN'];
        $this->oAuthToken = $GLOBALS['OAUTH_TOKEN'];
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
    }

    public function testCredentials() {
        $this->assertEquals($GLOBALS['SUBDOMAIN'] != '', true, 'Expecting GLOBALS[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['TOKEN'] != '', true, 'Expecting GLOBALS[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['USERNAME'] != '', true, 'Expecting GLOBALS[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
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

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $auditLog = $this->client->auditLog(24000361)->find(); // don't delete audit log #24000361
        $this->assertEquals(is_object($auditLog), true, 'Should return an object');
        $this->assertGreaterThan(0, $auditLog->audit_log->id, 'Returns a non-numeric id for audit_log');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
