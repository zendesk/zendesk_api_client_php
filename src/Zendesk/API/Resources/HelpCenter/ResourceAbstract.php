<?php

namespace Zendesk\API\Resources\HelpCenter;

/**
 * Abstract class for HelpCenter resources
 */
abstract class ResourceAbstract extends \Zendesk\API\Resources\ResourceAbstract
{

    /**
     * Appends the help_center/ prefix to resource names
     * @return string
     */
    protected function getResourceNameFromClass()
    {
        $resourceName = parent::getResourceNameFromClass();

        return 'help_center/' . $resourceName;
    }

    /**
     * Generate a route depending on a localization set
     * @param string $name
     * @param array $params
     */
    public function getRoute($name, array $params = [])
    {
        $routesWithLocale = ['findAll', 'find', 'create', 'update'];

        $locale = $this->getLocale();
        $resourceName = parent::getResourceNameFromClass();

        if (in_array($name, $routesWithLocale) && isset($locale)) {
            $originalResourceName = $this->resourceName;
            $this->resourceName   = "help_center/{$locale}/" . $resourceName;

            $route = parent::getRoute($name, $params);

            // Reset resourceName so it doesn't affect succeeding calls
            $this->resourceName = $originalResourceName;

            return $route;
        } else {
            return parent::getRoute($name, $params);
        }
    }
}
