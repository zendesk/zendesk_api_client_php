<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * OAuthTokens test class
 */
class OAuthTokensTest extends BasicTest {

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
        $tokens = $this->client->oauthTokens()->findAll();
        $this->assertEquals(is_object($tokens), true, 'Should return an object');
        $this->assertEquals(is_array($tokens->tokens), true, 'Should return an object containing an array called "tokens"');
        $this->assertGreaterThan(0, $tokens->tokens[0]->id, 'Returns a non-numeric id for tokens[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $id = 941; // don't delete this token
        $token = $this->client->oauthToken($id)->find();
        $this->assertEquals(is_object($token), true, 'Should return an object');
        $this->assertGreaterThan(0, $token->token->id, 'Returns a non-numeric id for token');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testRevoke() {
        $this->markTestSkipped(
            'Since there\'s no way to create a token programmatically, we can\'t test revoke'
        );
        $id = '123';
        $topic = $this->client->oauthClient($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
