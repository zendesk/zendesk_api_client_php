<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Create;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * Class Bookmarks
 */
class Bookmarks extends ResourceAbstract
{
    const OBJ_NAME = 'bookmark';

    use FindAll;
    use Create;
    use Delete;
}
