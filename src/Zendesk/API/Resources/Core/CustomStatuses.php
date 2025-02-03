<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * Class CustomStatuses
 */
class CustomStatuses extends ResourceAbstract
{
    use Find;

    use FindAll;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'custom_status';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'custom_statuses';
}
