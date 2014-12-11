<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * OAuthTokens test class
 */
class OAuthTokensTest extends BasicTest {

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
