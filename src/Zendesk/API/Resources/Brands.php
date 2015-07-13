<?php

namespace Zendesk\API\Resources;

use GuzzleHttp\Psr7\LazyOpenStream;
use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Brands class exposes methods as detailed on
 * https://developer.zendesk.com/rest_api/docs/core/brands
 *
 * @package Zendesk\API
 */
class Brands extends ResourceAbstract
{
    const OBJ_NAME = 'brand';
    const OBJ_NAME_PLURAL = 'brands';

    use Defaults;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'checkHostMapping' => "{$this->resourceName}/check_host_mapping.json",
            'updateImage'      => "{$this->resourceName}/{id}.json",
        ]);
    }

    /**
     * Check host mapping validity
     *
     * @param array $params
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function checkHostMapping(array $params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * Update a brand's image
     *
     * @param array $params
     *
     * @return array
     * @throws CustomException
     * @throws MissingParametersException
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function updateImage(array $params = [])
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (! $this->hasKeys($params, ['id', 'file'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'file']);
        }

        if (! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }

        $id = $params['id'];
        unset($params['id']);

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id]),
            [
                'method'    => 'PUT',
                'multipart' => [
                    [
                        'name'     => 'brand[photo][uploaded_data]',
                        'contents' => new LazyOpenStream($params['file'], 'r'),
                        'filename' => $params['file']
                    ]
                ],
            ]
        );

        return $response;
    }
}
