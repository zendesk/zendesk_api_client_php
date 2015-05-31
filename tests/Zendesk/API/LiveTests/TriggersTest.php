<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Triggers test class
 */
class TriggersTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $group_id;

    public function setUp()
    {
        // Prep:
        $group = $this->client->groups()->create(array(
            'name' => 'New Group'
        ));
        $this->group_id = $group->group->id;

        $trigger = $this->client->triggers()->create(array(
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
                    'field' => 'group_id',
                    'value' => $this->group_id
                )
            )
        ));
        $this->assertEquals(is_object($trigger), true, 'Should return an object');
        $this->assertEquals(is_object($trigger->trigger), true, 'Should return an object called "trigger"');
        $this->assertGreaterThan(0, $trigger->trigger->id, 'Returns a non-numeric id for trigger');
        $this->assertEquals($trigger->trigger->title, 'Roger Wilco', 'Title of test trigger does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $trigger->trigger->id;
    }

    public function testAll()
    {
        $triggers = $this->client->triggers()->findAll();
        $this->assertEquals(is_object($triggers), true, 'Should return an object');
        $this->assertEquals(is_array($triggers->triggers), true,
            'Should return an object containing an array called "triggers"');
        $this->assertGreaterThan(0, $triggers->triggers[0]->id, 'Returns a non-numeric id for triggers[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testActive()
    {
        $triggers = $this->client->triggers()->active();
        $this->assertEquals(is_object($triggers), true, 'Should return an object');
        $this->assertEquals(is_array($triggers->triggers), true,
            'Should return an object containing an array called "triggers"');
        $this->assertGreaterThan(0, $triggers->triggers[0]->id, 'Returns a non-numeric id for triggers[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $trigger = $this->client->trigger($this->id)->find();
        $this->assertEquals(is_object($trigger), true, 'Should return an object');
        $this->assertGreaterThan(0, $trigger->trigger->id, 'Returns a non-numeric id for trigger');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $trigger = $this->client->trigger($this->id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($trigger), true, 'Should return an object');
        $this->assertEquals(is_object($trigger->trigger), true, 'Should return an object called "trigger"');
        $this->assertGreaterThan(0, $trigger->trigger->id, 'Returns a non-numeric id for trigger');
        $this->assertEquals($trigger->trigger->title, 'Roger Wilco II', 'Title of test trigger does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a trigger id to test with. Did setUp fail?');
        $result = $this->client->trigger($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200',
            'Delete trigger does not return HTTP code 200');
        // Clean-up
        $result = $this->client->group($this->group_id)->delete();
    }

}
