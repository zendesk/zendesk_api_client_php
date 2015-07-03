<?php

namespace Zendesk\API\Resources;

use Zendesk\API\UtilityTraits\InstantiatorTrait;

class DynamicContentItems extends ResourceAbstract
{
    use InstantiatorTrait;

    const OBJ_NAME = 'item';
    const OBJ_NAME_PLURAL = 'items';

    protected $resourceName = 'dynamic_content/items';

    /**
     * {@inheritdoc}
     */
    public static function getValidRelations()
    {
        return [
            'variants' => DynamicContentItemVariants::class,
        ];
    }
}
