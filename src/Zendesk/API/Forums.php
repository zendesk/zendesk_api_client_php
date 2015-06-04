<?php

namespace Zendesk\API;

/**
 * The Forums class exposes forum information
 * @package Zendesk\API
 */
class Forums extends ResourceAbstract
{

    const OBJ_NAME = 'forum';
    const OBJ_NAME_PLURAL = 'forums';

    /**
     * @var ForumSubscriptions
     */
    protected $subscriptions;

    /**
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        parent::__construct($client);
        $this->subscriptions = new ForumSubscriptions($client);
    }

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */

    /**
     * @param int|null $id
     *
     * @return ForumSubscriptions
     */
    public function subscriptions($id = null)
    {
        return ($id != null ? $this->subscriptions->setLastId($id) : $this->subscriptions);
    }

    /**
     * @param int $id
     *
     * @return ForumSubscriptions
     */
    public function subscription($id)
    {
        return $this->subscriptions->setLastId($id);
    }
}
