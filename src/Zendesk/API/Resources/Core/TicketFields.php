<?php
namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * The TicketFields class exposes field management methods for tickets
 */
class TicketFields extends ResourceAbstract
{
    use InstantiatorTrait;

    use Defaults;

    protected $resourceName = 'ticket_fields';

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources()
    {
        return [
            'options' => TicketFieldsOptions::class,
        ];
    }
}
