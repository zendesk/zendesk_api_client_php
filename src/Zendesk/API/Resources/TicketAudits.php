<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;

/**
 * The TicketAudits class exposes read only audit methods
 * @package Zendesk\API
 */
class TicketAudits extends ResourceAbstract
{

    const OBJ_NAME = 'audit';
    const OBJ_NAME_PLURAL = 'audits';

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
          'findAll' => 'tickets/{ticket_id}/audits.json',
          'find'    => 'tickets/{ticket_id}/audits/{id}.json',
        ]);
    }

    /**
     * Returns all audits for a particular ticket
     *
     * @param array $params
     *
     * @return mixed
     * @throws MissingParametersException
     */
    public function findAll(array $params = array())
    {
        $params = $this->addChainedParametersToParams($params, ['ticket_id' => Tickets::class]);
        if ( ! $this->hasKeys($params, array('ticket_id'))) {
            throw new MissingParametersException(__METHOD__, array('ticket_id'));
        }

        return parent::findAll($params);
    }

    /**
     * Show a specific audit record
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return mixed
     */
    public function find($id = null, array $params = array())
    {
        if (empty($id)) {
            $id = $this->getChainedParameter(get_class($this));
        }

        $params = $this->addChainedParametersToParams(
          $params,
          [
            'ticket_id' => Tickets::class,
          ]
        );

        if ( ! $this->hasKeys($params, array('ticket_id'))) {
            throw new MissingParametersException(__METHOD__, array('ticket_id'));
        }

        $this->setAdditionalRouteParams(['ticket_id' => $params['ticket_id']]);

        return parent::find($id);
    }
}
