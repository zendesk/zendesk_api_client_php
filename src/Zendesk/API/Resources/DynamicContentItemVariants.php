<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Create;
use Zendesk\API\Traits\Resource\CreateMany;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;
use Zendesk\API\Traits\Resource\UpdateMany;

class DynamicContentItemVariants extends ResourceAbstract
{
    use CreateMany;
    use UpdateMany;

    use Find;
    use FindAll;
    use Create;
    use Delete;

    const OBJ_NAME = 'item';
    const OBJ_NAME_PLURAL = 'items';

    protected function setUpRoutes()
    {
        $this->setRoutes(
            [
                'findAll'    => 'dynamic_content/items/{item_id}/variants.json',
                'find'       => 'dynamic_content/items/{item_id}/variants/{id}.json',
                'create'     => 'dynamic_content/items/{item_id}/variants.json',
                'delete'     => 'dynamic_content/items/{item_id}/variants.json',
                'createMany' => 'dynamic_content/items/{item_id}/variants/create_many.json',
                'updateMany' => 'dynamic_content/items/{item_id}/variants/update_many.json',
            ]
        );
    }

    public function getRoute($name, array $params = [])
    {
        $params = $this->addChainedParametersToParams($params, ['item_id' => DynamicContentItems::class]);

        return parent::getRoute($name, $params);
    }
}
