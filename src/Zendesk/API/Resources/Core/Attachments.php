<?php

namespace Zendesk\API\Resources\Core;

use Psr\Http\Message\StreamInterface;
use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;

/**
 * The Attachments class exposes methods for uploading and retrieving attachments
 * @package Zendesk\API
 */
class Attachments extends ResourceAbstract
{
    use Delete;
    use Find;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'upload'       => "uploads.json",
            'deleteUpload' => "uploads/{token}.json",
        ]);
    }

    /**
     * Upload an attachment
     * $params must include:
     *    'file' - an attribute with the absolute local file path on the server
     *    'type' - the MIME type of the file
     * Optional:
     *    'token' - an existing token
     *     'name' - preferred filename
     *
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function upload(array $params)
    {
        if (! $this->hasKeys($params, ['file'])) {
            throw new MissingParametersException(__METHOD__, ['file']);
        }

        $isFileStream = $params['file'] instanceof StreamInterface;
        if (! $isFileStream  && ! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }

        if (! isset($params['name'])) {
            if ($isFileStream) {
                $params['name'] = basename($params['file']->getMetadata('uri'));
            } else {
                $params['name'] = basename($params['file']);
            }
        }

        $queryParams = ['filename' => $params['name']];
        if (isset($params['token'])) {
            $queryParams['token'] = $params['token'];
        }

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
                'method'      => 'POST',
                'contentType' => 'application/binary',
                'file'        => $params['file'],
                'queryParams' => $queryParams,
            ]
        );

        return $response;
    }

    /**
     * Delete a resource
     *
     * @param $token
     *
     * @return bool
     * @throws MissingParametersException
     * @throws \Exception
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function deleteUpload($token)
    {
        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__, ['token' => $token]),
            ['method' => 'DELETE']
        );

        return $response;
    }
}
