<?php

namespace Zendesk\API;

/**
 * The TicketComments class exposes comment methods for tickets
 * @package Zendesk\API
 */
class TicketComments extends ClientAbstract {

    const OBJ_NAME = 'comment';
    const OBJ_NAME_PLURAL = 'comments';

    /**
     * Returns all comments for a particular ticket
     *
     * @param array $params
     *
     * @throws MissingParametersException
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
        if(!$this->hasKeys($params, array('ticket_id'))) {
            throw new MissingParametersException(__METHOD__, array('ticket_id'));
        }
        $endPoint = Http::prepare('tickets/'.$params['ticket_id'].'/comments.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Make the specified comment private
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function makePrivate(array $params = array()) {
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id', 'ticket_id'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'ticket_id'));
        }
        $endPoint = Http::prepare('tickets/'.$params['ticket_id'].'/comments/'.$params['id'].'/make_private.json');
        $response = Http::send($this->client, $endPoint, null, 'PUT');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__, ' (hint: you can\'t make a private ticket private again)');
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */

    /**
     * @param array $params
     *
     * @throws CustomException
     */
    public function find(array $params = array()) {
        throw new CustomException('Method '.__METHOD__.' does not exist. Try $client->ticket(ticket_id)->comments()->findAll() instead.');
    }

}
