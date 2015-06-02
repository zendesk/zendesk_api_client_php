<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * SatisfactionRatings test class
 */
class SatisfactionRatingsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    protected $id, $ticket_id;

    public function setUp()
    {

        // Auth as end user
        $username = getenv('END_USER_USERNAME');
        $password = getenv('END_USER_PASSWORD');
        $client_end_user = new Client($this->subdomain, $username);
        $client_end_user->setAuth('password', $password);

        $testTicket = array(
            'subject' => 'Satisfaction Ratings Test',
            'comment' => array(
                'body' => 'Dette er for tilfredshed ratings test.'
            ),
            'priority' => 'normal'
        );
        $request = $client_end_user->requests()->create($testTicket);
        $this->ticket_id = $request->request->id;

        // Agent set ticket status to be solved
        $testTicket['status'] = 'solved';
        $this->client->ticket($this->ticket_id)->update($testTicket);

        $rating = $client_end_user->ticket($this->ticket_id)->satisfactionRatings()->create(array(
            'score' => 'good',
            'comment' => 'Awesome support'
        ));
        $this->assertEquals(is_object($rating), true, 'Should return an object');
        $this->assertEquals(is_object($rating->satisfaction_rating), true,
            'Should return an object called "satisfaction_rating"');
        $this->assertGreaterThan(0, $rating->satisfaction_rating->id,
            'Returns a non-numeric id for satisfaction_rating');
        $this->assertEquals($rating->satisfaction_rating->score, 'good', 'Score of test rating does not match');
        $this->assertEquals($client_end_user->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->id = $rating->satisfaction_rating->id;
    }

    public function testAll()
    {
        $ratings = $this->client->ticket($this->ticket_id)->satisfactionRatings()->findAll();
        $this->assertEquals(is_object($ratings), true, 'Should return an object');
        $this->assertEquals(is_array($ratings->satisfaction_ratings), true,
            'Should return an object containing an array called "satisfaction_ratings"');
        $this->assertGreaterThan(0, $ratings->satisfaction_ratings[0]->id,
            'Returns a non-numeric id for satisfaction_ratings[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $rating = $this->client->ticket($this->ticket_id)->satisfactionRating($this->id)->find();
        $this->assertEquals(is_object($rating), true, 'Should return an object');
        $this->assertGreaterThan(0, $rating->satisfaction_rating->id,
            'Returns a non-numeric id for satisfaction_rating');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->client->ticket($this->ticket_id)->delete();
    }

}
