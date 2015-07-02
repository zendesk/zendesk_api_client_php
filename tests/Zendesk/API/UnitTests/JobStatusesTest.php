<?php

namespace Zendesk\API\UnitTests;

class JobStatusesTest extends BasicTest
{
    /**
     * @expectedException Zendesk\API\Exceptions\RouteException
     */
    public function testNoFindAll()
    {
        $this->client->jobStatuses()->findAll([1]);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\RouteException
     */
    public function testNoCreate()
    {
        $this->client->jobStatuses()->create([1]);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\RouteException
     */
    public function testNoUpdate()
    {
        $this->client->jobStatuses()->update([1]);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\RouteException
     */
    public function testNoDelete()
    {
        $this->client->jobStatuses()->delete([1]);
    }
}
