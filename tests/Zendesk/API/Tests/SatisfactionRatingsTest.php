<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * SatisfactionRatings test class
 */
class SatisfactionRatingsTest extends BasicTest {

    public function testCredentials() {
        $this->assertEquals($_ENV['SUBDOMAIN'] != '', true, 'Expecting _ENV[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['TOKEN'] != '', true, 'Expecting _ENV[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['USERNAME'] != '', true, 'Expecting _ENV[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testCreate() {
        // Auth as end user
        $this->username = $_ENV['END_USER_USERNAME'];
        $this->password = $_ENV['END_USER_PASSWORD'];
        $this->token = $_ENV['TOKEN'];
        $this->oAuthToken = $_ENV['END_USER_OAUTH_TOKEN'];
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('password', $this->password);
        $rating = $this->client->ticket(200)->satisfactionRatings()->create(array(
            'score' => 'good',
            'comment' => 'Awesome support'
        ));
        $this->assertEquals(is_object($rating), true, 'Should return an object');
        $this->assertEquals(is_object($rating->satisfaction_rating), true, 'Should return an object called "satisfaction_rating"');
        $this->assertGreaterThan(0, $rating->satisfaction_rating->id, 'Returns a non-numeric id for satisfaction_rating');
        $this->assertEquals($rating->satisfaction_rating->score, 'good', 'Score of test rating does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $rating->satisfaction_rating->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $ratings = $this->client->ticket(200)->satisfactionRatings()->findAll();
        $this->assertEquals(is_object($ratings), true, 'Should return an object');
        $this->assertEquals(is_array($ratings->satisfaction_ratings), true, 'Should return an object containing an array called "satisfaction_ratings"');
        $this->assertGreaterThan(0, $ratings->satisfaction_ratings[0]->id, 'Returns a non-numeric id for satisfaction_ratings[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $rating = $this->client->ticket(200)->satisfactionRating($id)->find();
        $this->assertEquals(is_object($rating), true, 'Should return an object');
        $this->assertGreaterThan(0, $rating->satisfaction_rating->id, 'Returns a non-numeric id for satisfaction_rating');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
