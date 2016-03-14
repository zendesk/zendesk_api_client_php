<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

class SessionsTest extends BasicTest
{

    /**
     * Tests if the delete user session is accessible if the ID is passed
     */
    public function testBulkDeleteRouteNoChaining()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users()->sessions()->deleteUserSessions(12345);
        }, 'users/12345/sessions.json', 'DELETE');
    }

    /**
     * Tests if the delete user session endpoint is accessible if chained via the Users resource
     */
    public function testBulkDeleteRouteWithChaining()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->sessions()->deleteUserSessions();
        }, 'users/12345/sessions.json', 'DELETE');
    }

    /**
     * Tests if the delete route can be chained and called properly
     */
    public function testDeleteRoute()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->sessions()->delete(67890);
        }, 'users/12345/sessions/67890.json', 'DELETE');
    }

    /**
     * Tests if the find session route can be chained and called properly
     */
    public function testFindRoute()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->sessions()->find(67890);
        }, 'users/12345/sessions/67890.json', 'GET');
    }

    /**
     * Tests if the list sessions route can be called properly
     */
    public function testFindAllViaSessionsRoute()
    {
        $this->assertEndpointCalled(function () {
            $this->client->sessions()->findAll();
        }, 'sessions.json', 'GET');
    }

    /**
     * Tests if the list sessions route can be chained and called properly
     */
    public function testFindAllSessionsWithChaining()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->sessions()->findAll();
        }, 'users/12345/sessions.json', 'GET');
    }

    /**
     * Tests if the find route can be chained and called properly
     */
    public function testFindSession()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->sessions()->find(67890);
        }, 'users/12345/sessions/67890.json', 'GET');
    }
}
