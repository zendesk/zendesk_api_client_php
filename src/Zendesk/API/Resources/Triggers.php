<?php
namespace Zendesk\API\Resources;

use Zendesk\API\Http;

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
}
