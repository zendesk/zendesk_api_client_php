<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindMany;

/**
 * Class JobStatuses
 */
class JobStatuses extends ResourceAbstract
{
    const OBJ_NAME = 'job_status';
    const OBJ_NAME_PLURAL = 'job_statuses';

    use Find;

    use FindMany;
}
