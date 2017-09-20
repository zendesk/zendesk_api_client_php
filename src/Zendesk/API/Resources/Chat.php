<?php

namespace Zendesk\API\Resources;

use Zendesk\API\HttpClient;
use Zendesk\API\Resources\Chat\Apps;
use Zendesk\API\Resources\Chat\Integrations;
use Zendesk\API\Traits\Utility\ChainedParametersTrait;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * This class serves as a container to allow calls to $this->client->chat
 *
 * @method Apps apps()
 */
class Chat
{
    use ChainedParametersTrait;
    use InstantiatorTrait;

    public $client;

    /**
     * Sets the client to be used
     *
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public static function getValidSubResources()
    {
        return [
            'apps' => Apps::class,
            'integrations' => Integrations::class,
        ];
    }
}
