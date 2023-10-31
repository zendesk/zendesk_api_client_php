<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\FindAll;
use Zendesk\API\Traits\Utility\Pagination\SinglePageStrategy;

/**
 * The SharingAgreements class
 * https://developer.zendesk.com/rest_api/docs/core/sharing_agreements
 */
class SharingAgreements extends ResourceAbstract
{
    use FindAll;

    protected function paginationStrategyClass() {
        return SinglePageStrategy::class;
    }
}
