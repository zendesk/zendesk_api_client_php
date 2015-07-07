<?php

namespace Zendesk\API\Traits\Resource;

/**
 * This trait gives resources access to the default bulk CRUD methods.
 *
 * @package Zendesk\API\Traits\Resource
 */
trait DefaultsMany
{
    use FindMany;
    use CreateMany;
    use DeleteMany;
    use UpdateMany;
}
