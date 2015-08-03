<?php

namespace Zendesk\API\LiveTests;

/**
 * Auth test class
 */
class AuthTest extends BasicTest
{
    /**
     * Test the use of basic test
     */
    public function testBasicAuth()
    {
        $this->client->setAuth('basic', ['username' => $this->username, 'token' => $this->token]);
        $users = $this->client->users()->findAll();
        $this->assertTrue(isset($users->users), 'Should return a valid user object.');
    }

    /**
     * Test the use of basic test
     */
    public function testOAuth()
    {
        $this->client->setAuth('oauth', ['token' => $this->oAuthToken]);
        $users = $this->client->users()->findAll();
        $this->assertTrue(isset($users->users), 'Should return a valid user object.');
    }
}
