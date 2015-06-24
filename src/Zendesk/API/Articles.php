<?php
namespace Zendesk\API;

/**
 * The Articles class exposes article information
 * @package Zendesk\API
 */
class Articles extends ClientAbstract
{

    const OBJ_NAME = 'article';
    const OBJ_NAME_PLURAL = 'articles';

    /**
     * Find a specific article by id
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function find(array $params = array())
    {
        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $url = sprintf('help_center/%sarticles/%d.json', isset($params['locale']) ? $params['locale'] . '/' : '', $params['id']);
        $endPoint = Http::prepare($url, $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);

        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }
}
