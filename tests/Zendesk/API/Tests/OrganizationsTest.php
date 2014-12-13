<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Organizations test class
 */
class OrganizationsTest extends BasicTest {

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
        $organization = $this->client->organizations()->create(array(
            'name' => 'My Organization'
        ));
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertEquals(is_object($organization->organization), true, 'Should return an object called "organization"');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
        $this->assertEquals($organization->organization->name, 'My Organization', 'Name of test organization does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $organization->organization->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $organizations = $this->client->organizations()->findAll();
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true, 'Should return an object containing an array called "organizations"');
        $this->assertGreaterThan(0, $organizations->organizations[0]->id, 'Returns a non-numeric id for organizations[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $organization = $this->client->organization($id)->find();
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $organization = $this->client->organization($id)->update(array(
            'name' => 'My Organization II'
        ));
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertEquals(is_object($organization->organization), true, 'Should return an object called "organization"');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
        $this->assertEquals($organization->organization->name, 'My Organization II', 'Name of test organization does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testRelated($stack) {
        $id = array_pop($stack);
        $organizationRelated = $this->client->organization($id)->related();
        $this->assertEquals(is_object($organizationRelated), true, 'Should return an object');
        $this->assertEquals(is_object($organizationRelated->organization_related), true, 'Should return an object containing an array called "organization_related"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testAuthToken
     */
    public function testSearch() {
        $organizations = $this->client->organizations()->search(array('external_id' => 'my'));
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true, 'Should return an object containing an array called "organizations"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find an organization id to test with. Did testCreate fail?');
        $organization = $this->client->organization($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAutocomplete() {
        $organizations = $this->client->organizations()->autocomplete(array('name' => 'rog'));
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true, 'Should return an object containing an array called "organizations"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
