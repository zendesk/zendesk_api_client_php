<?php

namespace Zendesk\API\Resources;

use Zendesk\API\BulkTraits\BulkFindTrait;
use Zendesk\API\Traits\Resource\Find;

class JobStatuses extends ResourceAbstract
{
    const OBJ_NAME = 'job_status';
    const OBJ_NAME_PLURAL = 'job_statuses';

    use BulkFindTrait;
    use Find;
}
