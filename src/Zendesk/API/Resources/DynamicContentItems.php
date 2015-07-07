<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * Class DynamicContentItems
 *
 * @method DynamicContentItemVariants variants()
 */
class DynamicContentItems extends ResourceAbstract
{
    use InstantiatorTrait;
    use Defaults;
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
