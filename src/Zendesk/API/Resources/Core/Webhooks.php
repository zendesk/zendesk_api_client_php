<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Tags class exposes methods as detailed on https://developer.zendesk.com/api-reference/event-connectors/webhooks/webhooks/
 */
class Webhooks extends ResourceAbstract
{
    use Defaults;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'create'       => "{$this->resourceName}",
            'update'       => "{$this->resourceName}/{id}",
            'delete'       => "{$this->resourceName}",
        ]);
    }
}
