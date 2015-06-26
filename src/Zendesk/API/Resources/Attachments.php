<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;

/**
 * The Attachments class exposes methods for uploading and retrieving attachments
 * @package Zendesk\API
 */
class Attachments extends ResourceAbstract
{
    protected function setUpRoutes()
    {
        $this->setRoutes([
          'upload'       => "uploads.json",
          'deleteUpload' => "uploads/{token}.json",
          'delete'       => "{$this->resourceName}/{id}.json",
          'find'         => "{$this->resourceName}/{id}.json",
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
        if (! $this->hasKeys($params, array('file'))) {
            throw new MissingParametersException(__METHOD__, array('file'));
        }
        if (! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }
        if (! $params['name'] && strrpos($params['file'], '/') > - 1) {
            $path_array     = explode('/', $params['file']);
            $file_index     = count($path_array) - 1;
            $params['name'] = $path_array[$file_index];
        }
        if (! $params['name'] && strrpos($params['file'], '/') == false) {
            $params['name'] = $params['file'];
        }

        $queryParams = ['filename' => $params ['name']];
        if (isset($params['token'])) {
            $queryParams['token'] = $params['token'];
        }

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
            'method'      => 'POST',
            'contentType' => 'application/binary',
            'file'        => $params['file'],
            'queryParams' => $queryParams,
            ]
        );

        $this->client->setSideload(null);

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
        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, array('token' => $token)),
            ['method' => 'DELETE']
        );

        $this->client->setSideload(null);

        return $response;
    }
}
