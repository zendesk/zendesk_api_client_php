<?php
namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The TicketFields class exposes field management methods for tickets
 */
class TicketFields extends ResourceAbstract
{
    use Defaults;

    protected $resourceName = 'ticket_fields';
}
