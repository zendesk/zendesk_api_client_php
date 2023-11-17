<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Tags test class
 */
class TagsTest extends BasicTest
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
                'tags' => [$this->testResource0, $this->testResource1],
                'meta' => ['after_cursor' => '<after_cursor>', 'has_more' => true],

            ])),
            new Response(200, [], json_encode([
                'tags' => [$this->testResource2],
                'meta' => ['has_more' => false],

            ])),
        ]);
        $iterator = $this->client->tags()->iterator();

        $actual = $this->iterator_to_array($iterator);

        $this->assertCount(3, $actual);
        $this->assertEquals($this->testResource0['anyField'], $actual[0]->anyField);
        $this->assertEquals($this->testResource1['anyField'], $actual[1]->anyField);
        $this->assertEquals($this->testResource2['anyField'], $actual[2]->anyField);
    }

    /**
     * Test that the Tags resource class actually creates the correct routes:
     *
     * tickets/{id}/tags.json
     * topics/{id}/tags.json
     * organizations/{id}/tags.json
     * users/{id}/tags.json
     */
    public function testGetRoute()
    {
        $route = $this->client->tickets(12345)->tags()->getRoute('find', ['id' => 12345]);
        $this->assertEquals('tickets/12345/tags.json', $route);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\CustomException
     */
    public function testFindUnchained()
    {
        $this->client->tags()->find(1);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\CustomException
     */
    public function testFindNoChainedParameter()
    {
        $this->client->tickets()->tags()->find(1);
    }
}
