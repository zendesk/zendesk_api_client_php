<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * SharingAgreements test class
 */
class SharingAgreementsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    public function testAll() {
        $agreements = $this->client->sharingAgreements()->findAll();
        $this->assertEquals(is_object($agreements), true, 'Should return an object');
        $this->assertEquals(is_array($agreements->sharing_agreements), true, 'Should return an array of objects called "sharing_agreements"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
