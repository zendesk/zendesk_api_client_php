<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * Class DynamicContent
 *
 * @method DynamicContentItems items()
 */
class DynamicContent extends ResourceAbstract
{
    use InstantiatorTrait;

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources()
    {
        return [
            'items' => DynamicContentItems::class,
        ];
    }
}
