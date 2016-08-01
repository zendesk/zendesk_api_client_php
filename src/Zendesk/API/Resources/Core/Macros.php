<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Macros class exposes methods seen at http://developer.zendesk.com/documentation/rest_api/macros.html
 */
class Macros extends ResourceAbstract
{
    use Defaults;

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
     * @return \stdClass | null
     */
    public function findAllActive(array $params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
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

        return $this->client->get(
            $this->getRoute(__FUNCTION__, ['id' => $id])
        );
    }

    /**
     * Returns the full ticket object as it would be after applying the macro to the ticket.
     *
     * @param $id
     * @param $ticketId
     *
     * @return \stdClass | null
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

        return $this->client->get(
            $this->getRoute(__FUNCTION__, ['id' => $id, 'ticketId' => $ticketId])
        );
    }
}
