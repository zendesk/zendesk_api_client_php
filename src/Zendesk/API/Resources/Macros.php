<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;

/**
 * The Macros class exposes methods seen at http://developer.zendesk.com/documentation/rest_api/macros.html
 */
class Macros extends ResourceAbstract
{
    const OBJ_NAME = 'macro';
    const OBJ_NAME_PLURAL = 'macros';

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'findAllActive' => 'macros/active.json',
            'apply'         => 'macros/{id}/apply.json',
            'applyToTicket' => 'tickets/{ticketId}/macros/{id}/apply.json',
        ]);
    }

    /**
     * Lists all active shared and personal macros available to the current user
     *
     * @param array $params
     *
     * @throws \Exception
     * @return mixed
     */
    public function findAllActive(array $params = [])
    {
        $sideloads = $this->client->getSideload($params);

        $queryParams = Http::prepareQueryParams($sideloads, $params);

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, $params),
            ['queryParams' => $queryParams]
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Returns the changes the macro would make to a ticket.
     *
     * @param $id
     *
     * @return mixed
     * @throws MissingParametersException
     * @throws \Exception
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function apply($id)
    {
        if (empty($id)) {
            $id = $this->getChainedParameter(get_class($this));
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id])
        );
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Returns the full ticket object as it would be after applying the macro to the ticket.
     *
     * @param $id
     * @param $ticketId
     *
     * @return mixed
     * @throws MissingParametersException
     * @throws \Exception
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function applyToTicket($id, $ticketId)
    {
        if (empty($id)) {
            $id = $this->getChainedParameter(get_class($this));
        }

        if (empty($ticketId)) {
            $ticketId = $this->getChainedParameter(Tickets::class);
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, ['id', 'ticketId']);
        }

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id, 'ticketId' => $ticketId])
        );
        $this->client->setSideload(null);

        return $response;
    }
}
