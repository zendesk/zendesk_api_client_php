<?php
namespace Zendesk\API\Resources;

/**
 * The TicketFields class exposes field management methods for tickets
 */
class TicketFields extends ResourceAbstract
{

    const OBJ_NAME = 'ticket_field';
    const OBJ_NAME_PLURAL = 'ticket_fields';

    protected $resourceName = 'ticket_fields';
}
