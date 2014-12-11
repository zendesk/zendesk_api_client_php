<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * UserFields test class
 */
class UserFieldsTest extends BasicTest {

    public function testCredentials() {
        $this->assertEquals($_ENV['SUBDOMAIN'] != '', true, 'Expecting _ENV[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['TOKEN'] != '', true, 'Expecting _ENV[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['USERNAME'] != '', true, 'Expecting _ENV[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $userFields = $this->client->userFields()->findAll();
        $this->assertEquals(is_object($userFields), true, 'Should return an object');
        $this->assertEquals(is_array($userFields->user_fields), true, 'Should return an object containing an array called "user_fields"');
        $this->assertGreaterThan(0, $userFields->user_fields[0]->id, 'Returns a non-numeric id for user_fields[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $userField = $this->client->userField(13581)->find(); // don't delete user field #13581
        $this->assertEquals(is_object($userField), true, 'Should return an object');
        $this->assertEquals(is_object($userField->user_field), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $userField->user_field->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $userField = $this->client->userFields()->create(array(
            'type' => 'text',
            'title' => 'Support description',
            'description' => 'This field describes the support plan this user has',
            'position' => 0,
            'active' => true,
            'key' => 'support_description'.date("YmdHis")
        ));
        $this->assertEquals(is_object($userField), true, 'Should return an object');
        $this->assertEquals(is_object($userField->user_field), true, 'Should return an object called "user field"');
        $this->assertGreaterThan(0, $userField->user_field->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($userField->user_field->title, 'Support description', 'Title of test user field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $userField->user_field->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $userField = $this->client->userField($id)->update(array(
            'title' => 'Support description II'
        ));
        $this->assertEquals(is_object($userField), true, 'Should return an object');
        $this->assertEquals(is_object($userField->user_field), true, 'Should return an object called "user_field"');
        $this->assertGreaterThan(0, $userField->user_field->id, 'Returns a non-numeric id for user_field');
        $this->assertEquals($userField->user_field->title, 'Support description II', 'Name of test user field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $userField->user_field->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a user field id to test with. Did testCreate fail?');
        $view = $this->client->userField($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testReorder() {
        $view = $this->client->userFields()->reorder(array(13581));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }


}

?>
