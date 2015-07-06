<?php

namespace Zendesk\API\Resources;

use Zendesk\API\UtilityTraits\InstantiatorTrait;

/**
 * Class DynamicContentItems
 *
 * @method DynamicContentItemVariants variants()
 */
class DynamicContentItems extends ResourceAbstract
{
    use InstantiatorTrait;

    /**
     *
     */
    const OBJ_NAME = 'item';
    /**
     *
     */
    const OBJ_NAME_PLURAL = 'items';

    /**
     * @var string
     */
    protected $resourceName = 'dynamic_content/items';

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources()
    {
        return [
            'variants' => DynamicContentItemVariants::class,
        ];
    }
}
