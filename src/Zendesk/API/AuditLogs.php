<?php

namespace Zendesk\API;

/**
 * The AuditLogs class is as per http://developer.zendesk.com/documentation/rest_api/audit_logs.html
 * @package Zendesk\API
 */
class AuditLogs extends ClientAbstract {

    const OBJ_NAME = 'audit_log';
    const OBJ_NAME_PLURAL = 'audit_logs';

    /**
     * List all audit logs
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare('audit_logs.json', null, $params);
        $response = Http::send($this->client, $endPoint, $params, 'GET');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific audit log
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
        $endPoint = Http::prepare('audit_logs/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
