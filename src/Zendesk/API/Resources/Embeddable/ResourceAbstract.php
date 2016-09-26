<?php

namespace Zendesk\API\Resources\Embeddable;

use Zendesk\API\Traits\Resource\ResourceName;

/**
 * Abstract class for Embeddable resources
 */
abstract class ResourceAbstract extends \Zendesk\API\Resources\ResourceAbstract
{
    use ResourceName;

    /**
     * @var string
     **/
    protected $prefix = 'embeddable/api/';

    /**
     * @var string
     */
    protected $apiBasePath = '';
}
