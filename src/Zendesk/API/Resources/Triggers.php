<?php
namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Triggers class exposes field management methods for triggers
 */
class Triggers extends ResourceAbstract
{
    const OBJ_NAME = 'trigger';
    const OBJ_NAME_PLURAL = 'triggers';

    use Defaults;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoute('findActive', "{$this->resourceName}/active.json");
    }

    /**
     * Finds all active triggers
     *
     * @param array $params
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function findActive($params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }
}
