<?php

namespace Zendesk\API\Resources;

use Zendesk\API\HttpClient;
use Zendesk\API\Resources\HelpCenter\Categories;
use Zendesk\API\Traits\Utility\ChainedParametersTrait;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * This class serves as a container to allow $this->client->helpCenter
 *
 * @method Categories categories()
 */
class HelpCenter
{
    use ChainedParametersTrait;
    use InstantiatorTrait;

    public $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public static function getValidSubResources()
    {
        return [
            'categories' => Categories::class
        ];
    }
}
