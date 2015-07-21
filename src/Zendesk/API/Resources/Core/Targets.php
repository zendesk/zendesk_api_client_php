<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Tags class exposes methods as detailed on https://developer.zendesk.com/rest_api/docs/core/targets
 */
class Targets extends ResourceAbstract
{
    const OBJ_NAME = 'target';
    const OBJ_NAME_PLURAL = 'targets';

    use Defaults;
}
