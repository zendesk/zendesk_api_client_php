<?php
namespace Zendesk\API\UnitTests;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\CreateMany;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\DeleteMany;
use Zendesk\API\Traits\Resource\FindMany;
use Zendesk\API\Traits\Resource\UpdateMany;

/**
 * Class DummyResource
 */
class DummyResource extends ResourceAbstract
{
    const OBJ_NAME = 'dummy';
    const OBJ_NAME_PLURAL = 'dummies';

    use Defaults;
    use FindMany;
    use CreateMany;
    use UpdateMany;
    use DeleteMany;
}
