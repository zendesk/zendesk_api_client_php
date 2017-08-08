<?php

namespace Zendesk\API\Resources\Chat;

use Zendesk\API\Traits\Resource\ResourceName;

/**
 * Abstract class for Chat resources
 */
abstract class ResourceAbstract extends \Zendesk\API\Resources\ResourceAbstract
{
    use ResourceName;

    /**
     * @var string
     */
    protected $prefix = 'api/chat/';

    /**
     * @var string
     */
    protected $apiBasePath = '';
}
