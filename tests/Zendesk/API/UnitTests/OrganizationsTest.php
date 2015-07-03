<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class OrganizationsTest extends BasicTest
{
    public function testFindUserOrganizations()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->users(123)->organizations()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'users/123/organizations.json',
        ]);
    }

    /**
     * Tests if the default findAll route is still accessible
     */
    public function testFindAllOrganizations()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->organizations()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'organizations.json',
        ]);
    }

    public function testAutocomplete()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->organizations()->autocomplete('foo');

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'organizations/autocomplete.json',
            'queryParams' => ['name' => 'foo']
        ]);
    }

    public function testRelated()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->organizations()->related(123);

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'organizations/123/related.json',
            'queryParams' => []
        ]);
    }

    public function testSearchByExternalId()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->organizations()->search(123);

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'organizations/search.json',
            'queryParams' => ['external_id' => 123]
        ]);
    }
}
