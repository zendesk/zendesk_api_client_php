<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Utility\Pagination\SinglePageStrategy;

/**
 * The Tags class exposes methods as detailed on https://developer.zendesk.com/rest_api/docs/core/targets
 */
class Targets extends ResourceAbstract
{
    use Defaults;

    protected function paginationStrategyClass() {
        return SinglePageStrategy::class;
    }
}
