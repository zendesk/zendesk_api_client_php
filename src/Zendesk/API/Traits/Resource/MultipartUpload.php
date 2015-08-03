<?php

namespace Zendesk\API\Traits\Resource;

use GuzzleHttp\Psr7\LazyOpenStream;
use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Http;

/**
 * Trait MultipartUpload
 */
trait MultipartUpload
{
    /**
     * The using resource should define the upload name to use when uploading the file.
     *
     * @return String
     */
    abstract public function getUploadName();

    /**
     * The using resource should define the upload name to use when uploading the file.
     *
     * @return String
     */
    abstract public function getUploadRequestMethod();

    /**
     * Uploads an file with the given upload name.
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function upload(array $params, $routeKey = __FUNCTION__)
    {
        if (! array_key_exists('file', $params)) {
            throw new MissingParametersException(__METHOD__, ['file']);
        }

        if (! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }

        try {
            $route = $this->getRoute($routeKey, $params);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $this->setRoute(__FUNCTION__, $this->resourceName . '/uploads.json');
            $route = $this->resourceName . '/uploads.json';
        }

        $response = Http::send(
            $this->client,
            $route,
            [
                'method'    => $this->getUploadRequestMethod(),
                'multipart' => [
                    [
                        'name'     => $this->getUploadName(),
                        'contents' => new LazyOpenStream($params['file'], 'r'),
                        'filename' => $params['file']
                    ]
                ]
            ]
        );

        return $response;
    }
}
