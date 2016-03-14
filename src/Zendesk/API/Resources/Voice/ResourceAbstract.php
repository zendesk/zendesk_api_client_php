<?php

namespace Zendesk\API\Resources\Voice;

/**
 * Abstract class for Voice resources
 */
abstract class ResourceAbstract extends \Zendesk\API\Resources\ResourceAbstract
{
    /**
     * Appends the channels/voice/ prefix to resource names
     * @return string
     */
    protected function getResourceNameFromClass()
    {
        $resourceName = parent::getResourceNameFromClass();

        return 'channels/voice/' . $resourceName;
    }
}
