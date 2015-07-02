<?php

namespace Zendesk\API\BulkTraits;

use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Http;

/**
 * Allows resources to call a bulk destrpu endpoint.
 *
 */
trait BulkDeleteTrait
{
    /**
     * Show multiple resources
     *
     * @param array  $ids Array of IDs to delete
     * @param string $key Could be `id` or `external_id`
     *
     * @return mixed
     *
     */
    public function deleteMany(array $ids = [], $key = 'ids')
    {
        try {
            $route = $this->getRoute(__FUNCTION__);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/destroy_many.json';
            $this->setRoute('', $route);
        }

        $response = Http::sendWithOptions(
            $this->client,
            $route,
            [
                'method'      => 'DELETE',
                'queryParams' => [$key => implode(',', $ids)]
            ]
        );

        $this->client->setSideload(null);

        return $response;
    }
}
