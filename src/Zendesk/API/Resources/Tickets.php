<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;
use Zendesk\API\UtilityTraits\InstantiatorTrait;

/**
 * The Tickets class exposes key methods for reading and updating ticket data
 *
 * @package Zendesk\API
 *
 * @method TicketComments comments()
 */
class Tickets extends ResourceAbstract
{
    use InstantiatorTrait;

    const OBJ_NAME = 'ticket';
    const OBJ_NAME_PLURAL = 'tickets';

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
    /**
     * @var Tags
     */
    protected $tags;
    /*
     * Helpers:
     */

    /**
     * @var array
     */
    protected $lastAttachments = array();

    /**
     * {@inheritdoc}
     */
    public static function getValidRelations()
    {
        return [
          'comments' => TicketComments::class,
          'tags'     => Tags::class,
          'audits'   => TicketAudits::class,
        ];
    }

    /**
     * Wrapper for common GET requests
     *
     * @param       $route
     * @param array $params
     *
     * @return array
     * @throws ResponseException
     * @throws \Exception
     */
    private function _sendGetRequest($route, array $params = array())
    {
        $queryParams = Http::prepareQueryParams(
            $this->client->getSideload($params),
            $params
        );
        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute($route, $params),
            ['queryParams' => $queryParams]
        );

        if ((!is_object($response))
            || ($this->client->getDebug()->lastResponseCode != 200)
        ) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'findMany'            => 'tickets/show_many.json',
            'updateMany'          => 'tickets/update_many.json',
            'markAsSpam'          => 'tickets/{id}/mark_as_spam.json',
            'markManyAsSpam'      => 'tickets/mark_many_as_spam.json',
            'related'             => 'tickets/{id}/related.json',
            'deleteMany'          => 'tickets/destroy_many.json',
            'collaborators'       => 'tickets/{id}/collaborators.json',
            'incidents'           => 'tickets/{id}/incidents.json',
            'problems'            => 'problems.json',
            'export'              => 'exports/tickets.json',
            'problemAutoComplete' => 'problems/autocomplete.json'
        ]);
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

        $queryParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $queryParams['ids'] = implode(",", $params['ids']);

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute('findMany'),
            ['queryParams' => $queryParams]
        );

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
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('channels/twitter/tickets/' . $params['id'] . '/statuses.json' . (is_array($params['comment_ids']) ?
                '?' . implode(
                    ',',
                    $params['comment_ids']
                ) : ''), $this->client->getSideload($params));
        $response = Http::sendWithOptions($this->client, $endPoint);
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
            throw new MissingParametersException(
                __METHOD__,
                array('twitter_status_message_id', 'monitored_twitter_handle_id')
            );
        }
        $endPoint = Http::prepare('channels/twitter/tickets.json');
        $response = Http::sendWithOptions($this->client, $endPoint, array(self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(
                __METHOD__,
                ($this->client->getDebug()->lastResponseCode == 422 ? ' (hint: you can\'t create two tickets from the same tweet)'
                    : '')
            );
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Update a ticket or series of tickets
     *
     * @param array $updateResourceFields
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function update($id = null, array $updateResourceFields = [])
    {
        if (count($this->lastAttachments)) {
            $updateResourceFields['comment']['uploads'] = $this->lastAttachments;
            $this->lastAttachments = array();
        }

        return parent::update($id, $updateResourceFields);
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

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute('updateMany'),
            [
                'method'      => 'PUT',
                'queryParams' => $queryParams,
                'postFields'  => [$resourceUpdateName => $params]
            ]
        );

        return $response;
    }

    /**
     * Mark a ticket as spam and suspend the requester
     *
     * @param mixed $id The ticket ID, or an array of ticket ID's to mark as spam
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function markAsSpam($id = null)
    {
        $options = ['method' => 'PUT'];

        if (is_array($id)) {
            $options['queryParams']['ids'] = implode(',', $id);
            $route = $this->getRoute('markManyAsSpam');
        } else {
            $params = $this->addChainedParametersToParams(
                ['id' => $id],
                ['id' => get_class($this)]
            );
            $route = $this->getRoute('markAsSpam', $params);
        }

        $response = Http::sendWithOptions(
            $this->client,
            $route,
            $options
        );

        // Seems to be a bug in the service, it may respond with 422 even when it succeeds
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(
                __METHOD__,
                ($this->client->getDebug()->lastResponseCode == 422
                    ? ' (note: there\'s currently a bug in the service so this call may have succeeded; call tickets->find to see if it still exists.)'
                    : '')
            );
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
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }

        return $this->_sendGetRequest(__FUNCTION__, $params);
    }

    /**
     * Update a ticket or series of tickets
     *
     * @param array $ids
     *
     * @return mixed
     *
     */
    public function deleteMany(array $ids)
    {
        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute('deleteMany'),
            [
                'method'      => 'DELETE',
                'queryParams' => ['ids' => implode(',', $ids)]
            ]
        );

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
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }

        return $this->_sendGetRequest(__FUNCTION__, $params);
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
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }

        return $this->_sendGetRequest(__FUNCTION__, $params);
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
    public function problems(array $params = [])
    {
        return $this->_sendGetRequest(__FUNCTION__, $params);
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
        if (!$params['text']) {
            throw new MissingParametersException(__METHOD__, array('text'));
        }

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute('problemAutoComplete'),
            [
                'method'     => 'POST',
                'postFields' => ['text' => $params['text']]
            ]
        );

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

        $queryParams = ["start_time" => $params["start_time"]];

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute('export'),
            ["queryParams" => $queryParams]
        );

        $this->client->setSideload(null);

        return $response;
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
