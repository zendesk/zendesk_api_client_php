<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * JobStatuses test class
 */
class JobStatusesTest extends BasicTest {

    public function testCredentials() {
        $this->assertEquals($_ENV['SUBDOMAIN'] != '', true, 'Expecting _ENV[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['TOKEN'] != '', true, 'Expecting _ENV[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['USERNAME'] != '', true, 'Expecting _ENV[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $this->markTestSkipped(
            'Skipped for now because it requires a new job ID each time'
        );
        $id = 'dae40e506a55013162fd3c305bf76b24';
        $jobStatus = $this->client->jobStatus($id)->find();
        $this->assertEquals(is_object($jobStatus), true, 'Should return an object');
        $this->assertNotEmpty($jobStatus->job_status->id, 'Returns no id value for job_status');
        $this->assertEquals($jobStatus->job_status->status, 'completed', 'Returns an incorrect status for job_status');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
