<?php

namespace Zendesk\API;

/**
 * The Attachments class exposes methods for uploading and retrieving attachments
 * @package Zendesk\API
 */
class Attachments extends ClientAbstract {

    /**
     * Upload an attachment
     * $params must include:
     *    'file' - an attribute with the absolute local file path on the server
     *    'type' - the MIME type of the file
     * Optional:
     *    'optional_token' - an existing token
     *		'name' - preferred filename
     *
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function upload(array $params) {
        if(!$this->hasKeys($params, array('file'))) {
            throw new MissingParametersException(__METHOD__, array('file'));
        }
        if(!file_exists($params['file'])) {
            throw new CustomException('File '.$params['file'].' could not be found in '.__METHOD__);
        }
        if(!$params['name'] && strrpos($params['file'], '/') > -1) {
          $path_array = explode('/', $params['file']);
          $file_index = count($path_array) - 1;
          $params['name'] = $path_array[$file_index];
        }
        if(!$params['name'] && strrpos($params['file'], '/') == FALSE) {
	        $params['name'] = $params['file'];
        } 
        
        $endPoint = Http::prepare('uploads.json?filename='.$params['name'].(isset($params['optional_token']) ? '&token='.$params['optional_token'] : ''));
        $response = Http::send($this->client, $endPoint, array('filename' => $params['file']), 'POST', (isset($params['type']) ? $params['type'] : 'application/binary'));
       if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete one or more attachments by token or id
     * $params must include one of these:
     *        'token' - the token given to you after the original upload
     *        'id' - the id of the attachment
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function delete(array $params) {
        if(!$this->hasAnyKey($params, array('id', 'token'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'token'));
        }
        $endPoint = Http::prepare(($params['token'] ? 'uploads/'.$params['token'] : 'attachments/'.$params['id']).'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

    /**
     * Get a list of uploaded attachments (by id)
     * $params must include:
     *        'id' - the id of the attachment
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function find(array $params) {
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('attachments/'.$id.'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}