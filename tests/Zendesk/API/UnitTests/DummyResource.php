<?php
namespace Zendesk\API\UnitTests;

use Zendesk\API\BulkTraits\BulkCreateTrait;
use Zendesk\API\BulkTraits\BulkDeleteTrait;
use Zendesk\API\BulkTraits\BulkFindTrait;
use Zendesk\API\BulkTraits\BulkUpdateTrait;
use Zendesk\API\Resources\ResourceAbstract;

class DummyResource extends ResourceAbstract
{
    const OBJ_NAME = 'dummy';
    const OBJ_NAME_PLURAL = 'dummies';

    use BulkFindTrait;
    use BulkUpdateTrait;
    use BulkDeleteTrait;
    use BulkCreateTrait;
}
