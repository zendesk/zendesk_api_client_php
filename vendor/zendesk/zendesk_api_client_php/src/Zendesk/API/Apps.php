<?php

namespace Zendesk\API;

/**
* The Apps class exposes app management methods
*/
class Apps extends ClientAbstract {

    protected $installations;

    public function __construct($client) {
        parent::__construct($client);
        $this->installations = new AppInstallations($client);
    }

    /*
* Uploads an app - see http://developer.zendesk.com/documentation/rest_api/apps.html for workflow
* Removed '@'. from before $params['file']
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

    /*
* Create an app
*/
    public function create(array $params) {
        $endPoint = Http::prepare('apps.json');
        $response = Http::send($this->client, $endPoint, $params, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 202)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }
/*
* Get Job Status of App
*/
    public function getJob(array $params) {
        $endPoint = Http::prepare('apps/job_statuses/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint, $params, 'GET');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 202)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }
    
/*
* Install an App
*/


    public function install(array $params) {
        $endPoint = Http::prepare('apps/installations.json');
        $response = Http::send($this->client, $endPoint, $params, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }
    
/*
* Install an App
*/


    public function getInstallations() {
        $endPoint = Http::prepare('apps/installations.json');
        $response = Http::send($this->client, $endPoint, $params, 'GET');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }
    
        public function getInstallation(array $params) {
        $endPoint = Http::prepare('apps/installations/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint, $params, 'GET');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }
        
    /*
* Update an app
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
    
     public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('users/'.$params['id'].'.json', $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
* Delete an app
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

    /*
* Send an app notification
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
    public function installations($id = null) { return ($id != null ? $this->installations->setLastId($id) : $this->installations); }
    public function installation($id) { return $this->installations->setLastId($id); }

}