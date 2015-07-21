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

    const OBJ_NAME = 'ticket_field';
    const OBJ_NAME_PLURAL = 'ticket_fields';

    protected $resourceName = 'ticket_fields';
}
