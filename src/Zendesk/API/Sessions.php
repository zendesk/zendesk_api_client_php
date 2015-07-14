<?php

namespace Zendesk\API;

/**
 * The sessions class exposes view management methods
 * @package Zendesk\API
 */
class Sessions extends ClientAbstract
{

    const OBJ_NAME = 'session';
    const OBJ_NAME_PLURAL = 'sessions';

    /**
     * List all sessions
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll (array $params = array())
    {

        $uri = 'sessions.json';

        if (isset($params['user_id'])) {
            $uri = sprintf('users/%d/sessions.json', $params['user_id']);
        }

        $endPoint = Http::prepare($uri, null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific session
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function find(array $params = array())
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }

        if (!$this->hasKeys($params, array('id', 'user_id'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'user_id'));
        }

        $uri = sprintf('users/%d/sessions/%d.json', $params['user_id'], $params['id']);

        $endPoint = Http::prepare($uri);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete a session
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function delete(array $params)
    {
        if (!$this->hasKeys($params, array('user_id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id'));
        }

        $uri = 'users/me/sessions.json';

        if (isset($params['id'])) {
            $uri = sprintf('users/%d/sessions/%d.json', $params['user_id'], $params['id']);
        }

        $endPoint = Http::prepare($uri, null, $params);
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete all of a users sessions
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function deleteAll(array $params)
    {
        if (!$this->hasKeys($params, array('user_id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id'));
        }

        $uri = sprintf('users/%d/sessions.json', $params['user_id']);

        $endPoint = Http::prepare($uri, null, $params);
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }
}
