<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
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
     * {@inheritdoc}
     */
    protected $objectName = 'item';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'items';

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
