<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Organizations test class
 */
class OrganizationsTest extends BasicTest
{

    public function setUP()
    {
        // Create Organizations Mock Object
        $organization_mock_object = new \stdClass();
        $organization_mock_object->organization = new \stdClass();
        $organization_mock_object->organization->name = 'New Organization';
        $organization_mock_object->organization->id = 123456;
        $organization_mock_object->organization_related = new \stdClass();
        $organization_mock_object->job_status = new \stdClass();
        $organization_mock_object->job_status->id = 123456;
        $organization_mock_object->organizations = Array(
            $organization_mock_object->organization,
            clone $organization_mock_object->organization
        );

        // Set Variables that will be used in other tests
        $this->mock = $this->getMock('Organizations',
            array('findAll', 'find', 'create', 'createMany', 'update', 'delete', 'autocomplete', 'related', 'search'));
        $this->organizations = $organization_mock_object;
    }

    public function testFindAll()
    {
        // Test for FindAll Method - optionally accepts User_id as parameter
        $this->mock->expects($this->any())
            ->method('findAll')
            ->withConsecutive(array(), array($this->greaterThan(0)))
            ->will($this->returnValue($this->organizations));

        // Run Test with No parameter
        $organizations = $this->mock->findAll();
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true,
            'Should return an object containing an array called "organizations"');
        $this->assertGreaterThan(0, $organizations->organizations[0]->id,
            'Returns a non-numeric id for organizations[0]');

        // Run Test with User ID parameter
        $organizations = $this->mock->findAll(123456);
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true,
            'Should return an object containing an array called "organizations"');
        $this->assertGreaterThan(0, $organizations->organizations[0]->id,
            'Returns a non-numeric id for organizations[0]');
    }

    public function testFind()
    {
        // Test for Find Method - requires an Organization ID as parameter
        $this->mock->expects($this->any())
            ->method('find')
            ->with($this->greaterThan(0))
            ->will($this->returnValue($this->organizations));

        // Run Test with Organization ID parameter
        $organization = $this->mock->find(123456);
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertEquals(is_object($organization->organization), true,
            'Should return an object containing an object called "organization"');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
    }

    public function testCreate()
    {
        // Test for Create Method - requires an Organization Name as parameter
        $this->mock->expects($this->any())
            ->method('create')
            ->with($this->isType('string'))
            ->will($this->returnCallback(function ($name) {
                $this->organizations->organization->name = $name;

                return $this->organizations;
            }));

        $organization = $this->mock->create('Test Organization');
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertEquals(is_object($organization->organization), true,
            'Should return an object containing an object called "organization"');
        $this->assertGreaterThan(0, $organization->organization->id, 'Returns a non-numeric id for organization');
        $this->assertEquals($organization->organization->name, 'Test Organization',
            'Name of test organization does not match');

    }

    public function testCreateMany()
    {
        // Test for CreateMany Method - requires an Organization Name as parameter and should return a job status rather than an Organization object
        $this->mock->expects($this->any())
            ->method('createMany')
            ->with($this->isType('string'), $this->isType('string'))
            ->will($this->returnValue($this->organizations));

        $organizations = $this->mock->createMany('Organization I', 'Organization II');
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_object($organizations->job_status), true,
            'Should return an object containing an object called job_status"');
        $this->assertGreaterThan(0, $organizations->job_status->id,
            'Should return a non-numeric id for job status object');
    }

    public function testUpdate()
    {
        // Test for Update Method - requires an Organization ID and another value as parameters
        // Create a clone of the original object to ensure that the change results in a different object than the original
        $this->mock->expects($this->any())
            ->method('update')
            ->with($this->greaterThan(0), $this->isType('string'))
            ->will($this->returnCallback(function ($name) {
                $update_organization = clone $this->organizations->organization;
                $update_organization->name = $name;

                return $update_organization;
            }));

        $organization = $this->mock->update(123456, "Test Organization II");
        $this->assertEquals(is_object($organization), true, 'Should return an object');
        $this->assertEquals(is_object($organization), true,
            'Should return an object containing an object called "organization"');
        $this->assertGreaterThan(0, $organization->id, 'Returns a non-numeric id for organization');
        $this->assertNotEquals($this->organizations->organization, $organization,
            'Should return an organization that is not the same as the original');
    }

    public function testDelete()
    {
        // Test for Delete Method - requires an Organization ID as parameter
        $this->mock->expects($this->any())
            ->method('delete')
            ->with($this->greaterThan(0))
            ->will($this->returnValue(null));

        $organization = $this->mock->delete(123456);
        $this->assertEquals(null, $organization, 'Does not return a null object');
    }

    public function testAutocomplete()
    {
        // Test for Autocomplete Method - requires a partial Organization Name as parameter
        $this->mock->expects($this->any())
            ->method('autocomplete')
            ->with($this->isType('string'))
            ->will($this->returnValue($this->organizations));

        $organizations = $this->mock->autocomplete('New');
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true,
            'Should return an object containing an array called "organizations"');
        $this->assertGreaterThan(0, $organizations->organizations[0]->id, 'Returns a non-numeric id for organization');
        $this->assertContains('New', $organizations->organizations[0]->name,
            'Should return an string that contains the search term for organization name');
    }

    public function testRelated()
    {
        // Test for Related Method - requires an Organization ID as parameter
        $this->mock->expects($this->any())
            ->method('related')
            ->with($this->greaterThan(0))
            ->will($this->returnValue($this->organizations));

        $organization_related = $this->mock->related(123456);
        $this->assertEquals(is_object($organization_related), true, 'Should return an object');
        $this->assertEquals(is_object($organization_related->organization_related), true,
            'Should return an object containing an object called "organization_related"');
    }

    public function testSearch()
    {
        // Test for Search Method - requires an Organization External ID as parameter
        $this->mock->expects($this->any())
            ->method('search')
            ->with($this->isType('string'))
            ->will($this->returnValue($this->organizations));

        $organizations = $this->mock->search('12ef');
        $this->assertEquals(is_object($organizations), true, 'Should return an object');
        $this->assertEquals(is_array($organizations->organizations), true,
            'Should return an object containing an array called "organizations"');
        $this->assertGreaterThan(0, $organizations->organizations[0]->id,
            'Returns a non-numeric id for organizations[0]');
    }

}
