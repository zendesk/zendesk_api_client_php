<?php

namespace Zendesk\API;

/**
 * The Apps class exposes app management methods
 * @package Zendesk\API
 */
class Apps extends ClientAbstract {

    /**
     * @var AppInstallations
     */
    protected $installations;

    /**
     * @param Client $client
     */
     public function __construct(Client $client) {
        parent::__construct($client);
        $this->installations = new AppInstallations($client);
    }

    /**
     * Uploads an app - see http://developer.zendesk.com/documentation/rest_api/apps.html for workflow
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function upload(array $params) {
        if(!$this->hasKeys($params, array('file'))) {
            throw new MissingParametersException(__METHOD__, array('file'));
        }
        $endPoint = Http::prepare('apps/uploads.json');
        $response = Http::send($this->client, $endPoint, array('uploaded_data' => $params['file']), 'POST', (isset($params['type']) ? $params['type'] : 'application/binary'));
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create an app
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params) {
        $endPoint = Http::prepare('apps.json');
        $response = Http::send($this->client, $endPoint, $params, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Update an app
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function update(array $params) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('apps/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint, $params, 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete an app
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function delete(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('apps/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

    /**
     * Send an app notification
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function sendNotification(array $params) {
        $endPoint = Http::prepare('apps/notify.json');
        $response = Http::send($this->client, $endPoint, $params, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */

    /**
     * @param int|null $id
     *
     * @return AppInstallations
     */
    public function installations($id = null) { return ($id != null ? $this->installations->setLastId($id) : $this->installations); }

    /**
     * @param int $id
     *
     * @return AppInstallations
     */
    public function installation($id) { return $this->installations->setLastId($id); }

}
