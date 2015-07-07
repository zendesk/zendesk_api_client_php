<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Tags class exposes methods as detailed on https://developer.zendesk.com/rest_api/docs/core/targets
 *
 * @package Zendesk\API
 */
class Targets extends ResourceAbstract
{
    use Defaults;

    const OBJ_NAME = 'target';
    const OBJ_NAME_PLURAL = 'targets';
}
