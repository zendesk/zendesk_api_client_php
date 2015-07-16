<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

class TwitterHandles extends ResourceAbstract
{
    const OBJ_NAME = 'monitored_twitter_handle';
    const OBJ_NAME_PLURAL = 'monitored_twitter_handles';

    use Find;
    use FindAll;

    protected $resourceName = 'channels/twitter/monitored_twitter_handles';
}
