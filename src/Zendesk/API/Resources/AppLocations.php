<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

class AppLocations extends ResourceAbstract
{
    const OBJ_NAME = 'location';
    const OBJ_NAME_PLURAL = 'locations';

    use Find;
    use FindAll;

    protected $resourceName = 'apps/locations';
}
