<?php

namespace Zendesk\API\Resources\Voice;

use Zendesk\API\Traits\Resource\ResourceName;

/**
 * Abstract class for Voice resources
 */
abstract class ResourceAbstract extends \Zendesk\API\Resources\ResourceAbstract
{
    use ResourceName;

    /**
     * @var $prefix
     **/
    protected $prefix = 'channels/voice/';
}
