<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * Class CustomRoles
 * https://developer.zendesk.com/rest_api/docs/core/custom_roles
 */
class CustomRoles extends ResourceAbstract
{
    use FindAll;
}
