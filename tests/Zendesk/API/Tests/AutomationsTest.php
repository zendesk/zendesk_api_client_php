<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Automations test class
 */
class AutomationsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    protected $id;

    public function setUP(){
        $automation = $this->client->automations()->create(array(
            'title' => 'Roger Wilco',
            'all' => array(
                array(
                    'field' => 'status',
                    'operator' => 'is',
                    'value' => 'open'
                ),
                array(
                    'field' => 'priority',
                    'operator' => 'less_than',
                    'value' => 'high'
                )
            ),
            'actions' => array(
                array(
                    'field' => 'priority',
                    'value' => 'high'
                )
            )
        ));
        $this->assertEquals(is_object($automation), true, 'Should return an object');
        $this->assertEquals(is_object($automation->automation), true, 'Should return an object called "automation"');
        $this->assertGreaterThan(0, $automation->automation->id, 'Returns a non-numeric id for automation');
        $this->assertEquals($automation->automation->title, 'Roger Wilco', 'Name of test automation does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $automation->automation->id;
    }

    public function tearDown(){
        $this->assertGreaterThan(0, $this->id, 'Cannot find a automation id to test with. Did setUP fail?');
        $topic = $this->client->automation($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAll() {
        $automations = $this->client->automations()->findAll();
        $this->assertEquals(is_object($automations), true, 'Should return an object');
        $this->assertEquals(is_array($automations->automations), true, 'Should return an object containing an array called "automations"');
        $this->assertGreaterThan(0, $automations->automations[0]->id, 'Returns a non-numeric id for automations[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testActive() {
        $automations = $this->client->automations()->findAll(array('active' => true));
        $this->assertEquals(is_object($automations), true, 'Should return an object');
        $this->assertEquals(is_array($automations->automations), true, 'Should return an object containing an array called "automations"');
        $this->assertGreaterThan(0, $automations->automations[0]->id, 'Returns a non-numeric id for automations[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $automation = $this->client->automations($this->id)->find();
        $this->assertEquals(is_object($automation), true, 'Should return an object');
        $this->assertGreaterThan(0, $automation->automation->id, 'Returns a non-numeric id for automation');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate() {
        $automation = $this->client->automation($this->id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($automation), true, 'Should return an object');
        $this->assertEquals(is_object($automation->automation), true, 'Should return an object called "automation"');
        $this->assertGreaterThan(0, $automation->automation->id, 'Returns a non-numeric id for automation');
        $this->assertEquals($automation->automation->title, 'Roger Wilco II', 'Name of test automation does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }
}

?>
