<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The Activities class exposes methods for retrieving activities
 * https://developer.zendesk.com/rest_api/docs/core/activity_stream
 */
class Activities extends ResourceAbstract
{
    const OBJ_NAME = 'activity';
    const OBJ_NAME_PLURAL = 'activities';

    use Find;
    use FindAll;
}
