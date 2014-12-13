<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Autocomplete test class
 */
class AutocompleteTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    /**
     * @depends testAuthToken
     */
    public function testTags() {
        $tags = $this->client->autocomplete()->tags(array(
            'name' => 'att'
        ));
        $this->assertEquals(is_object($tags), true, 'Should return an object');
        $this->assertEquals(is_array($tags->tags), true, 'Should return an object containing an array called "tags"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
