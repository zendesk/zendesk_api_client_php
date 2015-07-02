<?php

namespace Zendesk\API\Resources;

use Zendesk\API\BulkTraits\BulkFindTrait;

class JobStatuses extends ResourceAbstract
{
    const OBJ_NAME = 'job_status';
    const OBJ_NAME_PLURAL = 'job_statuses';

    use BulkFindTrait;

    protected function setUpRoutes()
    {
        $this->setRoute('find', "{$this->resourceName}/{id}.json");
    }
}
