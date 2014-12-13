<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Macros test class
 */
class MacrosTest extends BasicTest {

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
        $macro = $this->client->macros()->create(array(
            'title' => 'Roger Wilco',
            'actions' => array(
                array(
                    'field' => 'status',
                    'value' => 'solved'
                )
            )
        ));
        $this->assertEquals(is_object($macro), true, 'Should return an object');
        $this->assertEquals(is_object($macro->macro), true, 'Should return an object called "macro"');
        $this->assertGreaterThan(0, $macro->macro->id, 'Returns a non-numeric id for macro');
        $this->assertEquals($macro->macro->title, 'Roger Wilco', 'Name of test macro does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $macro->macro->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $macros = $this->client->macros()->findAll();
        $this->assertEquals(is_object($macros), true, 'Should return an object');
        $this->assertEquals(is_array($macros->macros), true, 'Should return an object containing an array called "macros"');
        $this->assertGreaterThan(0, $macros->macros[0]->id, 'Returns a non-numeric id for macros[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testActive($stack) {
        $macros = $this->client->macros()->findAll(array('active' => true));
        $this->assertEquals(is_object($macros), true, 'Should return an object');
        $this->assertEquals(is_array($macros->macros), true, 'Should return an object containing an array called "macros"');
        $this->assertGreaterThan(0, $macros->macros[0]->id, 'Returns a non-numeric id for macros[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $macro = $this->client->macros($id)->find();
        $this->assertEquals(is_object($macro), true, 'Should return an object');
        $this->assertGreaterThan(0, $macro->macro->id, 'Returns a non-numeric id for macro');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $macro = $this->client->macro($id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($macro), true, 'Should return an object');
        $this->assertEquals(is_object($macro->macro), true, 'Should return an object called "macro"');
        $this->assertGreaterThan(0, $macro->macro->id, 'Returns a non-numeric id for macro');
        $this->assertEquals($macro->macro->title, 'Roger Wilco II', 'Name of test macro does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a macro id to test with. Did testCreate fail?');
        $topic = $this->client->macro($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
