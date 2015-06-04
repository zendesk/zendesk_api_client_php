<?php

namespace Zendesk\API;

/**
 * The Tickets class exposes key methods for reading and updating ticket data
 * @package Zendesk\API
 *
 * @method TicketAudits audits()
 * @method TicketComments comments()
 * @method TicketMetrics metrics()
 * @method SatisfactionRatings satisfactionRatings()
 */

class Tickets extends ResourceAbstract
{

    const OBJ_NAME = 'ticket';
    const OBJ_NAME_PLURAL = 'tickets';

    /**
     * @var TicketAudits
     */
    protected $audits;
    /**
     * @var TicketComments
     */
    protected $comments;
    /**
     * @var TicketMetrics
     */
    protected $metrics;
    /**
     * @var TicketImport
     */
    protected $import;
    /**
     * @var SatisfactionRatings
     */
    protected $satisfactionRatings;
    /*
     * Helpers:
     */

    /**
     * @var array
     */
    protected $lastAttachments = array();

    /**
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        parent::__construct($client);
        $this->audits = new TicketAudits($client);
        $this->comments = new TicketComments($client);
        $this->metrics = new TicketMetrics($client);
        $this->import = new TicketImport($client);
        $this->satisfactionRatings = new SatisfactionRatings($client);
    }

    /**
     * Find a specific ticket by id or series of ids
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
        $this->endpoint = 'tickets/show_many.json';

        $queryParams = ['ids' => implode(",", $params['ids'])];

        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $queryParams = array_merge($queryParams, $extraParams);

        $response = Http::send_with_options($this->client, $this->endpoint, ['queryParams' => $queryParams]);

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Find a specific twitter generated ticket by id
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findTwicket(array $params = array())
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('channels/twitter/tickets/' . $params['id'] . '/statuses.json' . (is_array($params['comment_ids']) ? '?' . implode(',',
                    $params['comment_ids']) : ''), $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Create a ticket
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params)
    {
        if (count($this->lastAttachments)) {
            $params['comment']['uploads'] = $this->lastAttachments;
            $this->lastAttachments = array();
        }

        return parent::create($params);
    }

    /**
     * Create a ticket from a tweet
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function createFromTweet(array $params)
    {
        if ((!$params['twitter_status_message_id']) || (!$params['monitored_twitter_handle_id'])) {
            throw new MissingParametersException(__METHOD__,
                array('twitter_status_message_id', 'monitored_twitter_handle_id'));
        }
        $endPoint = Http::prepare('channels/twitter/tickets.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__,
                ($this->client->getDebug()->lastResponseCode == 422 ? ' (hint: you can\'t create two tickets from the same tweet)' : ''));
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Update a ticket or series of tickets
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function update($id, array $params)
    {
        if (count($this->lastAttachments)) {
            $params['comment']['uploads'] = $this->lastAttachments;
            $this->lastAttachments = array();
        }

        return parent::update($id, $params);
    }

    /**
     * Update a ticket or series of tickets
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function updateMany(array $params)
    {
        if (count($this->lastAttachments)) {
            $params['comment']['uploads'] = $this->lastAttachments;
            $this->lastAttachments = array();
        }

        $resourceUpdateName = self::OBJ_NAME_PLURAL;
        $queryParams = [];
        if (isset($params['ids']) && is_array($params['ids'])) {
            $queryParams['ids'] = implode(",", $params['ids']);
            unset($params['ids']);

            $resourceUpdateName = self::OBJ_NAME;
        }

        $endPoint = 'tickets/update_many.json';

        $response = Http::send_with_options(
            $this->client,
            $endPoint,
            [
                'method' => 'PUT',
                'queryParams' => $queryParams,
                'postFields' => [$resourceUpdateName => $params]
            ]
        );

        return $response;
    }

    /**
     * Mark a ticket as spam
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function markAsSpam(array $params = array())
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('tickets/' . $id . '/mark_as_spam.json');
        $response = Http::send($this->client, $endPoint, null, 'PUT');
        // Seems to be a bug in the service, it may respond with 422 even when it succeeds
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__,
                ($this->client->getDebug()->lastResponseCode == 422 ? ' (note: there\'s currently a bug in the service so this call may have succeeded; call tickets->find to see if it still exists.)' : ''));
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Get related ticket information
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function related(array $params = array())
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('tickets/' . $id . '/related.json', $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Delete a ticket or series of tickets
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function delete(array $params = array())
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }

        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }

        $id = $params['id'];


        $queryParams = [];

        if (is_array($id)) {
            $endPoint = 'tickets/destroy_many.json';
            $queryParams['ids'] = implode(",", $id);
        } else {
            $endPoint = 'tickets/' . $id . '.json';
        }

        $response = Http::send($this->client, $endPoint, $queryParams, [], 'DELETE');

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * List collaborators for a ticket
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function collaborators(array $params)
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('tickets/' . $id . '/collaborators.json', $this->client->getSideload($params),
            $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * List incidents for a ticket
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function incidents(array $params)
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('tickets/' . $id . '/incidents.json', $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }


    /**
     * List all problem tickets
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function problems(array $params)
    {
        $endPoint = Http::prepare('problems.json', $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Add a problem autocomplete
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function problemAutoComplete(array $params)
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (!$this->hasKeys($params, array('id', 'text'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'text'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('tickets/' . $id . '/problems/autocomplete.json');
        $response = Http::send($this->client, $endPoint, array('text' => $params['text']), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Incremental ticket exports with a supplied start_time
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function export(array $params)
    {
        if (!$params['start_time']) {
            throw new MissingParametersException(__METHOD__, array('start_time'));
        }

        $endPoint = 'exports/tickets.json';
        $queryParams = ["start_time" => $params["start_time"]];

        $response = Http::send($this->client, $endPoint, $queryParams, [], "GET");

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * For testing of incremental tickets only
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function exportSample(array $params)
    {
        if (!$params['start_time']) {
            throw new MissingParametersException(__METHOD__, array('start_time'));
        }
        $endPoint = Http::prepare('exports/tickets/sample.json?start_time=' . $params['start_time']);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Generic method to object getter. Since all objects are protected, this method
     * exposes a getter function with the same name as the protected variable, for example
     * $client->tickets can be referenced by $client->tickets()
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
     * Syntactic sugar methods:
     * Handy aliases:
     */

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

    /**
     * @param array $params
     *
     * @throws ResponseException
     *
     * @return mixed
     */
    public function import(array $params)
    {
        return $this->import->import($params);
    }

    /**
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return Tickets
     */
    public function attach(array $params = array())
    {
        if (!$this->hasKeys($params, array('file'))) {
            throw new MissingParametersException(__METHOD__, array('file'));
        }

        $upload = $this->client->attachments()->upload($params);

        if ((!is_object($upload->upload)) || (!$upload->upload->token)) {
            throw new ResponseException(__METHOD__);
        }
        $this->lastAttachments[] = $upload->upload->token;

        return $this;
    }

}
