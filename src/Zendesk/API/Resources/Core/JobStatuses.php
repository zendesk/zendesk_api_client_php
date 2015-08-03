<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindMany;

/**
 * Class JobStatuses
 */
class JobStatuses extends ResourceAbstract
{
    use Find;

    use FindMany;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'job_status';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'job_statuses';
}
