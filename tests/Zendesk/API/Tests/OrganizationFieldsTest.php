<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * OrganizationFields test class
 */
class OrganizationFieldsTest extends BasicTest {

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
    public function testCreate() {
        $organizationFields = $this->client->organizationFields()->create(array(
            'type' => 'text',
            'title' => 'Support description',
            'description' => 'This field describes the support plan this user has',
            'position' => 0,
            'active' => true,
            'key' => 'support_description'.date("YmdHis")
        ));
        $this->assertEquals(is_object($organizationFields), true, 'Should return an object');
        $this->assertEquals(is_object($organizationFields->organization_field), true, 'Should return an object called "organization_field"');
        $this->assertGreaterThan(0, $organizationFields->organization_field->id, 'Returns a non-numeric id for organization_field');
        $this->assertEquals($organizationFields->organization_field->title, 'Support description', 'Name of test organization field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $organizationFields->organization_field->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $organizationFields = $this->client->organizationFields()->findAll();
        $this->assertEquals(is_object($organizationFields), true, 'Should return an object');
        $this->assertEquals(is_array($organizationFields->organization_fields), true, 'Should return an object containing an array called "organization_fields"');
        $this->assertGreaterThan(0, $organizationFields->organization_fields[0]->id, 'Returns a non-numeric id for organization_fields[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $organizationField = $this->client->organizationField($id)->find();
        $this->assertEquals(is_object($organizationField), true, 'Should return an object');
        $this->assertGreaterThan(0, $organizationField->organization_field->id, 'Returns a non-numeric id for organization_field');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $organizationField = $this->client->organizationField($id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($organizationField), true, 'Should return an object');
        $this->assertEquals(is_object($organizationField->organization_field), true, 'Should return an object called "organization_field"');
        $this->assertGreaterThan(0, $organizationField->organization_field->id, 'Returns a non-numeric id for organization_field');
        $this->assertEquals($organizationField->organization_field->title, 'Roger Wilco II', 'Title of test organization field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find an organization field id to test with. Did testCreate fail?');
        $organizationField = $this->client->organizationField($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testReorder() {
        $result = $this->client->organizationFields()->reorder(array('organization_field_ids' => array(14382, 14342)));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
