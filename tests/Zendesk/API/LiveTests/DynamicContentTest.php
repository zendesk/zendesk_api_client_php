<?php

namespace Zendesk\API\LiveTests;

/**
 * Macros test class
 */
class DynamicContentTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id;

    public function setUp()
    {
        $number = strval(time());

        $dynamicContent = $this->client->dynamicContent()->create(array(
            'name' => "Test Content Name {$number}",
            'content' => "test content content",
            'default_locale_id' => 1176
        ));
        $this->assertEquals(is_object($dynamicContent), true, 'Should return an object');
        $this->assertEquals(is_object($dynamicContent->item), true, 'Should return an object called "item"');
        $this->assertGreaterThan(0, $dynamicContent->item->id, 'Returns a non-numeric id for item');
        $this->assertEquals($dynamicContent->item->name, "Test Content Name {$number}",
            'Name of test item does not match');
        $this->assertEquals('201', $this->client->getDebug()->lastResponseCode, 'Does not return HTTP code 201');
        $this->id = $dynamicContent->item->id;
    }

    public function testFindAll()
    {
        $dynamicContents = $this->client->dynamicContent()->findAll();
        $this->assertEquals(is_object($dynamicContents), true, 'Should return an object');
        $this->assertEquals(is_array($dynamicContents->items), true,
            'Should return an object containing an array called "items"');
        $this->assertGreaterThan(0, $dynamicContents->items[0]->id, 'Returns a non-numeric id for items[0]');
        $this->assertEquals('200', $this->client->getDebug()->lastResponseCode, 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a item id to test with. Did setUp fail?');
        $dynamicContent = $this->client->dynamicContent($this->id)->delete();
        $this->assertEquals('200', $this->client->getDebug()->lastResponseCode, 'Does not return HTTP code 200');
    }

}
