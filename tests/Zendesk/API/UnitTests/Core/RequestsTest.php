<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Requests test class
 */
class RequestsTest extends BasicTest
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
                'requests' => [$this->testResource0, $this->testResource1],
                'meta' => ['after_cursor' => '<after_cursor>', 'has_more' => true],

            ])),
            new Response(200, [], json_encode([
                'requests' => [$this->testResource2],
                'meta' => ['has_more' => false],

            ])),
        ]);

        $iterator = $this->client->requests()->iterator();

        $actual = $this->iterator_to_array($iterator);
        $this->assertCount(3, $actual);
        $this->assertEquals($this->testResource0['anyField'], $actual[0]->anyField);
        $this->assertEquals($this->testResource1['anyField'], $actual[1]->anyField);
        $this->assertEquals($this->testResource2['anyField'], $actual[2]->anyField);
    }

    /**
     * test findAll method
     */
    public function testFindAll()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAll();
        }, 'requests.json');
    }

    /**
     * Test findAllOpen method
     */
    public function testFindAllOpen()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllOpen();
        }, 'requests/open.json');
    }

    /**
     * Test findAllSolved method
     */
    public function testFindAllSolved()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllSolved();
        }, 'requests/solved.json');
    }

    /**
     * Test findAllCCd method
     */
    public function testFindAllCCd()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllCCd();
        }, 'requests/ccd.json');
    }

    /**
     * Test findAll method with a chained user
     */
    public function testFindAllChainedUser()
    {
        $userId = 8373;

        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->requests()->findAll();
        }, "users/{$userId}/requests.json");
    }

    /**
     * Test findAll method with chained organization
     */
    public function testFindAllChainedOrganization()
    {
        $organizationId = 8373;

        $this->assertEndpointCalled(function () use ($organizationId) {
            $this->client->organizations($organizationId)->requests()->findAll();
        }, "organizations/{$organizationId}/requests.json");
    }

    /**
     * Test search method
     */
    public function testSearch()
    {
        $queryParams = [
            'query'           => 'camera',
            'organization_id' => 1,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->requests()->search($queryParams);
        }, 'requests/search.json', 'GET', ['queryParams' => $queryParams]);
    }
}
