<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;

class TicketForms extends ResourceAbstract
{
    const OBJ_NAME = 'ticket_forms';
    const OBJ_NAME_PLURAL = 'ticket_forms';

    protected $resourceName = 'ticket_forms';

    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'clone'   => 'ticket_forms/{id}/clone.json',
            'reorder' => 'ticket_forms/reorder.json'
        ]);
    }

    /**
     * Clones an existing ticket form (can't use 'clone' as method name)
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws Zendesk\API\Exceptions\MissingParametersException
     *
     * @return mixed
     */
    function cloneForm($id = null)
    {
        $class = get_class($this);
        if (empty($id)) {
            $id = $this->getChainedParameter($class);
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute('clone', ['id' => $id]),
            ['method' => 'POST']
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Reorder Ticket forms
     *
     * @param array $ticketFormIds
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    function reorder(array $ticketFormIds)
    {
        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['postFields' => ['ticket_form_ids' => $ticketFormIds], 'method' => 'PUT']
        );

        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }

        $this->client->setSideload(null);

        return $response;
    }
}
