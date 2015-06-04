<?php

namespace Zendesk\API;

/**
 * The Topics class exposes topic information
 * @package Zendesk\API
 *
 * @method TopicComments comments()
 * @method TopicSubscriptions subscriptions()
 * @method TopicVotes votes()
 */
class Topics extends ResourceAbstract
{

    const OBJ_NAME = 'topic';
    const OBJ_NAME_PLURAL = 'topics';

    /**
     * @var TopicComments
     */
    protected $comments;
    /**
     * @var TopicSubscriptions
     */
    protected $subscriptions;
    /**
     * @var TopicVotes
     */
    protected $votes;

    /**
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        parent::__construct($client);
        $this->comments = new TopicComments($client);
        $this->subscriptions = new TopicSubscriptions($client);
        $this->votes = new TopicVotes($client);
    }

    /**
     * Find a specific topic by id or series of ids
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findMany(array $params = array())
    {
        $this->endpoint = 'topics/show_many.json';

        $queryParams = ['ids' => implode(",", $params['ids'])];

        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $queryParams = array_merge($queryParams, $extraParams);

        $response = Http::send_with_options($this->client, $this->endpoint, ['queryParams' => $queryParams]);

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Import a topic (same as create but without notifications)
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function import(array $params)
    {
        $this->endpoint = 'import/topics.json';
        $response = Http::send_with_options($this->client, $this->endpoint,
            ['postFields' => [self::OBJ_NAME => $params], 'method' => 'POST']
        );

        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Generic method to object getter
     *
     * @param $name
     * @param $arguments
     *
     * @throws CustomException
     */
    public function __call($name, $arguments)
    {
        if (isset($this->$name)) {
            return ((isset($arguments[0])) && ($arguments[0] != null) ? $this->$name->setLastId($arguments[0]) : $this->$name);
        }
        $namePlural = $name . 's'; // try pluralize
        if (isset($this->$namePlural)) {
            return $this->$namePlural->setLastId($arguments[0]);
        } else {
            throw new CustomException("No method called $name available in " . __CLASS__);
        }
    }

    /**
     * @param int|null $id
     *
     * @return Tags
     */
    public function tags($id = null)
    {
        return ($id != null ? $this->client->tags()->setLastId($id) : $this->client->tags());
    }

    /**
     * @param $id
     *
     * @return Tags
     */
    public function tag($id)
    {
        return $this->client->tags()->setLastId($id);
    }

}
