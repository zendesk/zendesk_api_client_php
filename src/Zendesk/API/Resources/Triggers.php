<?php
namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Triggers class exposes field management methods for triggers
 */
class Triggers extends ResourceAbstract
{
    use Defaults;

    const OBJ_NAME = 'trigger';
    const OBJ_NAME_PLURAL = 'triggers';

    protected function setUpRoutes()
    {
        $this->setRoute('findActive', "{$this->resourceName}/active.json");
    }

    public function findActive($params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }
}
