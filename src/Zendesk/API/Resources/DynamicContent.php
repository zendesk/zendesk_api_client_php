<?php

namespace Zendesk\API\Resources;

use Zendesk\API\UtilityTraits\InstantiatorTrait;

class DynamicContent extends ResourceAbstract
{
    use InstantiatorTrait;

    /**
     * {@inheritdoc}
     */
    public static function getValidRelations()
    {
        return [
            'items' => DynamicContentItems::class,
        ];
    }

    protected function setUpRoutes()
    {
        // Empty routes, this class serves as a possible entry point to dynamic content items
    }
}
