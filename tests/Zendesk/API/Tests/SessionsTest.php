<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Locals test class
 */
class SessionTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    public function testFindAllCurrent()
    {
        $sessions = $this->client->sessions()->findAll();
        $this->assertEquals(is_object($sessions), true, 'Should return an object');
        $this->assertEquals(is_array($sessions->sessions), true, 'Should return an array of objects called "sessions"');
        $this->assertGreaterThan(0, $sessions->sessions[0]->id, 'Returns a non-numeric id for sessions');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        return $sessions->sessions[0]->user_id;
    }

    /**
     * @depends testFindAllCurrent
     */
    public function testFindAllUser($userId)
    {
        $sessions = $this->client->sessions()->findAll(['user_id' => $userId]);
        $this->assertEquals(is_object($sessions), true, 'Should return an object');
        $this->assertEquals(is_array($sessions->sessions), true, 'Should return an array of objects called "sessions"');
        $this->assertGreaterThan(0, $sessions->sessions[0]->id, 'Returns a non-numeric id for sessions');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        return $sessions->sessions[0]->id;
    }

    /**
     * @depends testFindAllUser
     */
    public function testFind($sessionId)
    {
        $session = $this->client->session(1)->find($sessionId);
        $this->assertEquals(is_object($session), true, 'Should return an object');
        $this->assertEquals(is_object($session->session[0]), true, 'Should return an array called "session"');
        $this->assertGreaterThan(0, $session->session[0]->id, 'Returns a non-numeric id for session');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        return $session->session[0]->user_id;
    }

    /**
     * @depends testFind
     */
    public function testDeleteCurrent($userId)
    {
        $session = $this->client->sessions()->delete(array('user_id' => $userId));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        return array('id' => $session->session[0]->id, 'user_id' => $userId);
    }

    /**
     * @depends testDeleteCurrent
     */
    public function testDelete($ids)
    {
        $session = $this->client->sessions()->delete(array('id' => $ids['id'], 'user_id' => $ids['userId']));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');

        return array('id' => $session->session[0]->id, 'user_id' => $userId);
    }

    /**
     * @depends testDeleteCurrent
     */
    public function testDeleteAll($ids)
    {
        $session = $this->client->sessions()->delete(array('id' => $ids['id'], 'user_id' => $ids['userId']));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }
}
