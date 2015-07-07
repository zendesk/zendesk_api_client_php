<?php
namespace Zendesk\API\UnitTests;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\DefaultsMany;

/**
 * Class DummyResource
 */
class DummyResource extends ResourceAbstract
{
    const OBJ_NAME = 'dummy';
    const OBJ_NAME_PLURAL = 'dummies';

    use Defaults;
    use DefaultsMany;
}
