<?php

namespace Zendesk\API\Resources;

use Zendesk\API\HttpClient;
use Zendesk\API\Resources\Voice\PhoneNumbers;
use Zendesk\API\Traits\Utility\ChainedParametersTrait;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * This class serves as a container to allow $this->client->helpCenter
 *
 * @method PhoneNumbers phoneNumbers()
 */
class Voice
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
     * @inheritdoc
     * @return array
     */
    public static function getValidSubResources()
    {
        return [
            'phoneNumbers' => PhoneNumbers::class,
        ];
    }
}
