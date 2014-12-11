<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * GroupMemberships test class
 */
class GroupMembershipsTest extends BasicTest {

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
    public function testAll() {
        $groupMemberships = $this->client->groupMemberships()->findAll();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true, 'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id, 'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAllByUser() {
        $groupMemberships = $this->client->user(454094082)->groupMemberships()->findAll();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true, 'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id, 'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAllByGroup() {
        $groupMemberships = $this->client->group(21526762)->members()->findAll();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true, 'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id, 'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $groupMembership = $this->client->groupMembership(22534232)->find(); // don't delete group membership #22534232
        $this->assertEquals(is_object($groupMembership), true, 'Should return an object');
        $this->assertGreaterThan(0, $groupMembership->group_membership->id, 'Returns a non-numeric id for group');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $groupMembership = $this->client->groupMemberships()->create(array(
            'group_id' => 21699482,
            'user_id' => 455057612
        ));
        $this->assertEquals(is_object($groupMembership), true, 'Should return an object');
        $this->assertEquals(is_object($groupMembership->group_membership), true, 'Should return an object called "group_membership"');
        $this->assertGreaterThan(0, $groupMembership->group_membership->id, 'Returns a non-numeric id for group_membership');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $groupMembership->group_membership->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testMakeDefault(array $stack) {
        $id = array_pop($stack);
        $groupMemberships = $this->client->user(455057612)->groupMembership($id)->makeDefault();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true, 'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id, 'Returns a non-numeric id for groups[0]');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a group membership id to test with. Did testCreate fail?');
        $view = $this->client->groupMembership($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
