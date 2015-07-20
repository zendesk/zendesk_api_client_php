<?php
namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\CreateMany;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\DeleteMany;
use Zendesk\API\Traits\Resource\FindMany;
use Zendesk\API\Traits\Resource\MultipartUpload;
use Zendesk\API\Traits\Resource\UpdateMany;

/**
 * Class DummyResource
 */
class DummyResource extends ResourceAbstract
{
    const OBJ_NAME = 'dummy';
    const OBJ_NAME_PLURAL = 'dummies';

    use Defaults;
    use MultipartUpload;

    use FindMany;
    use CreateMany;
    use UpdateMany;
    use DeleteMany;

    /**
     * The using resource should define the upload name to use when uploading the file.
     *
     * @return String
     */
    public function getUploadName()
    {
        return 'upload';
    }

    /**
     * The using resource should define the upload name to use when uploading the file.
     *
     * @return String
     */
    public function getUploadRequestMethod()
    {
        return 'POST';
    }
}
