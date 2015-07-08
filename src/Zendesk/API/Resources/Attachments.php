<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;

/**
 * The Attachments class exposes methods for uploading and retrieving attachments
 * @package Zendesk\API
 */
class Attachments extends ResourceAbstract
{
    use Find;
    use Delete;

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
     *    'optional_token' - an existing token
     *        'name' - preferred filename
     *
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws \Exception
     * @return mixed
     */
    public function upload(array $params)
    {
        if (! $this->hasKeys($params, ['file'])) {
            throw new MissingParametersException(__METHOD__, ['file']);
        } elseif (! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }

        if (! isset($params['name'])) {
            $params['name'] = basename($params['file']);
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
