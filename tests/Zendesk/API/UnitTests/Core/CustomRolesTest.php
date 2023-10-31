<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

class CustomRolesTest extends BasicTest
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
        // Single Page
        $this->mockApiResponses([
            new Response(200, [], json_encode([
                'custom_roles' => [$this->testResource0, $this->testResource1, $this->testResource2]

            ]))
        ]);

        $iterator = $this->client->customRoles()->iterator();

        $actual = iterator_to_array($iterator);
        $this->assertCount(3, $actual);
        $this->assertEquals($this->testResource0['anyField'], $actual[0]->anyField);
        $this->assertEquals($this->testResource1['anyField'], $actual[1]->anyField);
        $this->assertEquals($this->testResource2['anyField'], $actual[2]->anyField);
    }
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->customRoles(), 'findAll'));
    }
}
