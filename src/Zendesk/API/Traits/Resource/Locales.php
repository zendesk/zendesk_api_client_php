<?php

namespace Zendesk\API\Traits\Resource;

/**
 * Trait Locale
 */
trait Locales
{

    /**
     * Used for setting up the locale
     * @var locale
     */
    protected $locale;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return Locales
     */
    public function setLocale($locale)
    {
        if (is_string($locale)) {
            $this->locale = $locale;
        }

        return $this;
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
            $temp = explode('/', $resourceName);
            $className = $temp[count($temp) - 1];
            $this->resourceName   = "help_center/$locale/" . $className;

            $route = parent::getRoute($name, $params);

            // Reset resourceName so it doesn't affect succeeding calls
            $this->resourceName = $originalResourceName;

            return $route;
        } else {
            return parent::getRoute($name, $params);
        }
    }

    /**
     * Updates a resource's source_locale property
     *
     * @param int $categoryId The category to update
     * @param string $sourceLocale The new source_locale
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function updateSourceLocale($categoryId, $sourceLocale)
    {
        if (empty($categoryId)) {
            $categoryId = $this->getChainedParameter(get_class($this));
        }

        return $this->client->put(
            $this->getRoute(__FUNCTION__, ["{$this->objectName}Id" => $categoryId]),
            ["{$this->objectName}_locale" => $sourceLocale]
        );
    }
}
