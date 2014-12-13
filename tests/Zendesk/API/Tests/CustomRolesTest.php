<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Custom Roles test class
 */
class CustomRolesTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $customRoles = $this->client->customRoles()->findAll();
        $this->assertEquals(is_object($customRoles), true, 'Should return an object');
        $this->assertEquals(is_array($customRoles->custom_roles), true, 'Should return an object containing an array called "custom_roles"');
        $this->assertGreaterThan(0, $customRoles->custom_roles[0]->id, 'Returns a non-numeric id for custom_roles[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
