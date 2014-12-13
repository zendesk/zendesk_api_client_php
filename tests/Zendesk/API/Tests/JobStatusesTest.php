<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * JobStatuses test class
 */
class JobStatusesTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
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
