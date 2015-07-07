<?php

namespace Zendesk\API\Traits\Resource;

/**
 * This trait gives resources access to the default CRUD methods.
 *
 * @package Zendesk\API\Traits\Resource
 */
trait Defaults
{
    use Find;
    use FindAll;
    use Delete;
    use Create;
    use Update;
}
