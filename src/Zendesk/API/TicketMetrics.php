<?php

namespace Zendesk\API;

/**
 * The TicketMetrics class exposes metrics methods for tickets
 * @package Zendesk\API
 */
class TicketMetrics extends ClientAbstract {

    const OBJ_NAME = 'ticket_metric';
    const OBJ_NAME_PLURAL = 'ticket_metrics';

    /**
     * List all ticket metrics
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        $endPoint = Http::prepare((isset($params['ticket_id']) ? 'tickets/'.$params['ticket_id'].'/metrics.json' : 'ticket_metrics.json'), null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific ticket metric
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
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasAnyKey($params, array('id', 'ticket_id'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'ticket_id'));
        }
        $endPoint = Http::prepare((isset($params['ticket_id']) ? 'tickets/'.$params['ticket_id'].'/metrics.json' : 'ticket_metrics/'.$params['id'].'.json'));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
