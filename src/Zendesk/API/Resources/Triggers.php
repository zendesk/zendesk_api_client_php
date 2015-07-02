<?php
namespace Zendesk\API\Resources;

/**
 * The Triggers class exposes field management methods for triggers
 */
class Triggers extends ResourceAbstract
{
    const OBJ_NAME = 'trigger';
    const OBJ_NAME_PLURAL = 'triggers';

    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoute('findActive', "{$this->resourceName}/active.json");
    }

    public function findActive($params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }
}
