<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * JobStatuses test class
 */
class JobStatusesTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    public function testFind()
    {
        $testTicket = array(
            'subject' => 'The quick brown fox jumps over the lazy dog',
            'comment' => array(
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $ticket = $this->client->tickets()->create($testTicket);
        $ticket2 = $this->client->tickets()->create($testTicket);

        $testUpdateTicket['id'] = array($ticket->ticket->id, $ticket2->ticket->id);
        $testUpdateTicket['subject'] = 'Updated subject';
        $testUpdateTicket['priority'] = 'urgent';

        $testJobStatus = $this->client->tickets()->update($testUpdateTicket);

        $id = $testJobStatus->job_status->id;
        $jobStatus = $this->client->jobStatus($id)->find();
        $this->assertEquals(is_object($jobStatus), true, 'Should return an object');
        $this->assertNotEmpty($jobStatus->job_status->id, 'Returns no id value for job_status');
        // $this->assertEquals($jobStatus->job_status->status, 'working', 'Returns an incorrect status for job_status');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        $ticket = $this->client->ticket($ticket->ticket->id)->delete();
        $ticket = $this->client->ticket($ticket2->ticket->id)->delete();
    }

}
