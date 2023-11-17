<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Class OrganizationsTest
 */
class OrganizationsTest extends BasicTest
{
    protected $testResource0;
    protected $testResource1;
    protected $testResource2;

    public function setUp()
    {
        $this->testResource0 = ['anyField'  => 'Any field 0'];
        $this->testResource1 = ['anyField'  => 'Any field 1'];
        $this->testResource2 = ['anyField'  => 'Any field 2'];
        parent::setUp();
    }

    public function testIterator()
    {
        // CBP
        $this->mockApiResponses([
            new Response(200, [], json_encode([
                'organizations' => [$this->testResource0, $this->testResource1],
                'meta' => ['after_cursor' => '<after_cursor>', 'has_more' => true],

            ])),
            new Response(200, [], json_encode([
                'organizations' => [$this->testResource2],
                'meta' => ['has_more' => false],

            ])),
        ]);

        $iterator = $this->client->organizations()->iterator();

        $actual = $this->iterator_to_array($iterator);
        $this->assertCount(3, $actual);
        $this->assertEquals($this->testResource0['anyField'], $actual[0]->anyField);
        $this->assertEquals($this->testResource1['anyField'], $actual[1]->anyField);
        $this->assertEquals($this->testResource2['anyField'], $actual[2]->anyField);
    }

    /**
     * test for FindAll with chained user resource.
     */
    public function testFindUserOrganizations()
    {
        $userId = 232;
        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->organizations()->findAll();
        }, "users/{$userId}/organizations.json");
    }

    /**
     * Tests if the default findAll route is still accessible
     */
    public function testFindAllOrganizations()
    {
        $this->assertEndpointCalled(function () {
            $this->client->organizations()->findAll();
        }, 'organizations.json');
    }

    /**
     * Test for autocomplete method
     */
    public function testAutocomplete()
    {
        $this->assertEndpointCalled(function () {
            $this->client->organizations()->autocomplete('foo');
        }, 'organizations/autocomplete.json', 'GET', ['queryParams' => ['name' => 'foo']]);
    }

    /**
     * Test related method
     */
    public function testRelated()
    {
        $resourceId = 123;
        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->organizations()->related($resourceId);
        }, "organizations/{$resourceId}/related.json");
    }

    /**
     * Test for search method
     */
    public function testSearchByExternalId()
    {
        $externalId = 123;
        $this->assertEndpointCalled(function () use ($externalId) {
            $this->client->organizations()->search($externalId);
        }, 'organizations/search.json', 'GET', ['queryParams' => ['external_id' => 123]]);
    }
}
