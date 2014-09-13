<?php

namespace Zendesk\API;

/**
 * The Requests class exposes request management methods
 * Note: you must authenticate as a user!
 */
class Requests extends ClientAbstract {

    const OBJ_NAME = 'request';
    const OBJ_NAME_PLURAL = 'requests';


    /**
     * @var RequestComments
     */
    protected $comments;
    
    /**
     * @param Client $client
     */
    public function __construct(Client $client) {
        parent::__construct($client);
        $this->comments = new RequestComments($client);
    }

    /**
     * List all requests
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare(
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/requests' :
                (isset($params['user_id']) ? 'users/'.$params['user_id'].'/requests' :
                (isset($params['ccd']) ? 'requests/ccd' :
                (isset($params['solved']) ? 'requests/solved' :
                (isset($params['open']) ? 'requests/open' : 'requests'))))
            ).'.json'.(isset($params['status']) ? '?status='.$params['status'] : ''), $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific request
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('requests/'.$params['id'].'.json', $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a new request
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params) {
        $endPoint = Http::prepare('requests.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Update a request
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
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('requests/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
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
     * @return RequestComments
     */
    public function comments($id = null) { return ($id != null ? $this->comments->setLastId($id) : $this->comments); }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function comment($id) { return $this->comments->setLastId($id); }

    /**
     * @param array $params
     *
     * @throws ResponseException*
     *
     * @return mixed
     */
    public function open(array $params = array()) { $params['open'] = true; return $this->findAll($params); }

    /**
     * @param array $params
     *
     * @throws ResponseException
     *
     * @return mixed
     */
    public function solved(array $params = array()) { $params['solved'] = true; return $this->findAll($params); }

    /**
     * @param array $params
     *
     * @throws ResponseException
     *
     * @return mixed
     */
    public function ccd(array $params = array()) { $params['ccd'] = true; return $this->findAll($params); }

}
