<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Categories test class
 */
class CategoriesTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $category = $this->client->categories()->create(array(
            'name' => 'My Category'
        ));
        $this->assertEquals(is_object($category), true, 'Should return an object');
        $this->assertEquals(is_object($category->category), true, 'Should return an object called "category"');
        $this->assertGreaterThan(0, $category->category->id, 'Returns a non-numeric id for category');
        $this->assertEquals($category->category->name, 'My Category', 'Name of test category does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $category->category->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $categories = $this->client->categories()->findAll();
        $this->assertEquals(is_object($categories), true, 'Should return an object');
        $this->assertEquals(is_array($categories->categories), true, 'Should return an object containing an array called "categories"');
        $this->assertGreaterThan(0, $categories->categories[0]->id, 'Returns a non-numeric id for categories[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $category = $this->client->category($id)->find();
        $this->assertEquals(is_object($category), true, 'Should return an object');
        $this->assertGreaterThan(0, $category->category->id, 'Returns a non-numeric id for category');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $category = $this->client->category($id)->update(array(
            'name' => 'My Category II'
        ));
        $this->assertEquals(is_object($category), true, 'Should return an object');
        $this->assertEquals(is_object($category->category), true, 'Should return an object called "category"');
        $this->assertGreaterThan(0, $category->category->id, 'Returns a non-numeric id for category');
        $this->assertEquals($category->category->name, 'My Category II', 'Name of test category does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a category id to test with. Did testCreate fail?');
        $view = $this->client->category($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
