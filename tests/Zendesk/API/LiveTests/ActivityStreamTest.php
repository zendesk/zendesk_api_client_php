<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * ActivityStream test class
 */
class ActivityStreamTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $ticket_id;

    public function setUP()
    {

        $username = getenv('END_USER_USERNAME');
        $password = getenv('END_USER_PASSWORD');
        $client_end_user = new Client($this->subdomain, $username);
        $client_end_user->setAuth('password', $password);

        $testTicket = array(
            'subject' => 'Activity Stream Test',
            'comment' => array(
                'body' => 'ce est biche Actions test.'
            ),
            'priority' => 'normal'
        );
        $request = $client_end_user->requests()->create($testTicket);
        $this->ticket_id = $request->request->id;
    }

    public function testAll()
    {
        $activities = $this->client->activities()->findAll();
        $this->assertEquals(is_object($activities), true, 'Should return an object');
        $this->assertEquals(is_array($activities->activities), true,
            'Should return an array of objects called "activities"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $activity_id = $this->client->activities()->findAll()->activities[0]->id;
        $activity = $this->client->activity($activity_id)->find();
        $this->assertEquals(is_object($activity), true, 'Should return an object');
        $this->assertEquals(is_object($activity->activity), true, 'Should return an objects called "activity"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->client->tickets($this->ticket_id)->delete();
    }

}
