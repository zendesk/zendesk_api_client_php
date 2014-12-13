<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Triggers test class
 */
class TriggersTest extends BasicTest {
    
    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        // Prep:
        $group = $this->client->groups()->create(array(
            'name' => 'New Group'
        ));
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
                    'value' => $group->group->id
                )
            )
        ));
        $this->assertEquals(is_object($trigger), true, 'Should return an object');
        $this->assertEquals(is_object($trigger->trigger), true, 'Should return an object called "trigger"');
        $this->assertGreaterThan(0, $trigger->trigger->id, 'Returns a non-numeric id for trigger');
        $this->assertEquals($trigger->trigger->title, 'Roger Wilco', 'Title of test trigger does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $trigger->trigger->id;
        $stack = array($id, $group->group->id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $triggers = $this->client->triggers()->findAll();
        $this->assertEquals(is_object($triggers), true, 'Should return an object');
        $this->assertEquals(is_array($triggers->triggers), true, 'Should return an object containing an array called "triggers"');
        $this->assertGreaterThan(0, $triggers->triggers[0]->id, 'Returns a non-numeric id for triggers[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testActive($stack) {
        $triggers = $this->client->triggers()->active();
        $this->assertEquals(is_object($triggers), true, 'Should return an object');
        $this->assertEquals(is_array($triggers->triggers), true, 'Should return an object containing an array called "triggers"');
        $this->assertGreaterThan(0, $triggers->triggers[0]->id, 'Returns a non-numeric id for triggers[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $trigger = $this->client->trigger($stack[0])->find();
        $this->assertEquals(is_object($trigger), true, 'Should return an object');
        $this->assertGreaterThan(0, $trigger->trigger->id, 'Returns a non-numeric id for trigger');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $trigger = $this->client->trigger($stack[0])->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($trigger), true, 'Should return an object');
        $this->assertEquals(is_object($trigger->trigger), true, 'Should return an object called "trigger"');
        $this->assertGreaterThan(0, $trigger->trigger->id, 'Returns a non-numeric id for trigger');
        $this->assertEquals($trigger->trigger->title, 'Roger Wilco II', 'Title of test trigger does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $this->assertGreaterThan(0, $stack[0], 'Cannot find a trigger id to test with. Did testCreate fail?');
        $result = $this->client->trigger($stack[0])->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete trigger does not return HTTP code 200');
        // Clean-up
        $result = $this->client->group($stack[1])->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete group does not return HTTP code 200');
    }

}

?>
