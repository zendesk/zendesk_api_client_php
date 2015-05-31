<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Organizations test class
 */
class OrganizationsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $number;

    public function setUP()
    {
        $this->number = strval(time());
        $organization = $this->client->organizations()->create(array(
            'name' => 'My New Organization' . $this->number
        ));
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertEquals(is_object($organization->organization), true,
            'Should return an object called "organization"');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
        $this->assertEquals($organization->organization->name, 'My New Organization' . $this->number,
            'Name of test organization does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $organization->organization->id;
    }

    public function testAll()
    {
        $organizations = $this->client->organizations()->findAll();
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true,
            'Should return an object containing an array called "organizations"');
        $this->assertGreaterThan(0, $organizations->organizations[0]->id,
            'Returns a non-numeric id for organizations[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $organization = $this->client->organization($this->id)->find();
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $organization = $this->client->organization($this->id)->update(array(
            'name' => 'My Organization II'
        ));
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertEquals(is_object($organization->organization), true,
            'Should return an object called "organization"');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
        $this->assertEquals($organization->organization->name, 'My Organization II',
            'Name of test organization does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testRelated()
    {
        $organizationRelated = $this->client->organization($this->id)->related();
        $this->assertEquals(is_object($organizationRelated), true, 'Should return an object');
        $this->assertEquals(is_object($organizationRelated->organization_related), true,
            'Should return an object containing an array called "organization_related"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testSearch()
    {
        $organizations = $this->client->organizations()->search(array('external_id' => 'my'));
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true,
            'Should return an object containing an array called "organizations"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAutocomplete()
    {
        $organizations = $this->client->organizations()->autocomplete(array('name' => 'rog'));
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true,
            'Should return an object containing an array called "organizations"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find an organization id to test with. Did setUp fail?');
        $organization = $this->client->organization($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
