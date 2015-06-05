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

    protected function setUpRoutes()
    {
        parent::setUpRoutes();
        $this->setRoute('findAll', 'tickets/{ticket_id}/metrics.json');
    }

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
        $chainedParameters = $this->getChainedParameters();
        if (array_key_exists(get_class($this->client->tickets()), $chainedParameters)) {
            $params['ticket_id'] = $chainedParameters[get_class($this->client->tickets())];
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
        $chainedParameters = $this->getChainedParameters();

        if (array_key_exists(get_class($this->client->tickets()), $chainedParameters)) {
            $params['ticket_id'] = $chainedParameters[get_class($this->client->tickets())];
        }

        return parent::find($params);
    }

}
