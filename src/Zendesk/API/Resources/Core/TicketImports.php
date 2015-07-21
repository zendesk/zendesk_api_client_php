<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Create;
use Zendesk\API\Traits\Resource\CreateMany;

/**
 * The TicketImport class exposes import methods for tickets
 */
class TicketImports extends ResourceAbstract
{
    const OBJ_NAME = 'ticket';
    const OBJ_NAME_PLURAL = 'tickets';

    use Create;

    use CreateMany;

    /**
     * Sets up the available routes for the resource.
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'create'     => 'imports/tickets.json',
            'createMany' => 'imports/tickets/create_many.json',
        ]);
    }
}
