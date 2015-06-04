<?php

namespace Zendesk\API;

/**
 * The TicketMetrics class exposes metrics methods for tickets
 * @package Zendesk\API
 */
class TicketMetrics extends ResourceAbstract
{
    protected $endpoint = 'ticket_metrics';

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
    public function findAll(array $params = array())
    {
        if ($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
            $this->endpoint = "tickets/{$params['ticket_id']}/metrics.json";
        }

        return parent::findAll($params);
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
    public function find(array $params = array())
    {
        if ($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
            $this->endpoint = "tickets/{$params['ticket_id']}/metrics.json";
        }

        return parent::find($params);
    }

}
