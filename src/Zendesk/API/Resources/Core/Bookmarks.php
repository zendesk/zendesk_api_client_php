<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Create;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * Class Bookmarks
 */
class Bookmarks extends ResourceAbstract
{
    use FindAll;
    use Create;
    use Delete;
}
