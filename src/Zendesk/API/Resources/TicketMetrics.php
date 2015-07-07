<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The TicketMetrics class exposes metrics methods for tickets
 *
 * @package Zendesk\API\Resources
 */
class TicketMetrics extends ResourceAbstract
{
    use FindAll;
    use Find;

    const OBJ_NAME = 'ticket_metric';
    const OBJ_NAME_PLURAL = 'ticket_metrics';

    protected $resourceName = 'ticket_metrics';

    protected function setUpRoutes()
    {
        $this->setRoute('findAll', "{$this->resourceName}.json");
        $this->setRoute('find', "{$this->resourceName}/{id}.json");
    }

    public function getRoute($name, array $params = [])
    {
        if ('find' === $name || 'findAll' === $name) {
            $lastChained = $this->getChainedParameter('Zendesk\API\Resources\Tickets');

            if (! empty($lastChained)) {
                return "tickets/$lastChained/metrics.json";
            }
        }

        return parent::getRoute($name, $params);
    }
}
