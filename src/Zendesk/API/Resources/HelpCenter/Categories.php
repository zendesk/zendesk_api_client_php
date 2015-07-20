<?php

namespace Zendesk\API\Resources\HelpCenter;

use Zendesk\API\Traits\Resource\Defaults;

class Categories extends ResourceAbstract
{
    const OBJ_NAME = 'category';
    const OBJ_NAME_PLURAL = 'categories';

    use Defaults;

    private $locale;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Categories
     */
    public function setLocale($locale)
    {
        if (is_string($locale)) {
            $this->locale = $locale;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoute($name, array $params = [])
    {
        $routesWithLocale = ['findAll', 'find', 'create', 'update'];

        $locale = $this->getLocale();
        if (in_array($name, $routesWithLocale) && isset($locale)) {
            $originalResourceName = $this->resourceName;
            $this->resourceName   = "help_center/{$locale}/categories";

            $route = parent::getRoute($name, $params);

            // Reset resourceName so it doesn't affect succeeding calls
            $this->resourceName = $originalResourceName;

            return $route;
        } else {
            return parent::getRoute($name, $params);
        }
    }

    /**
     * Updates a categories source_locale property
     *
     * @param $categoryId   The category to update
     * @param $sourceLocale The new source_locale
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
            $this->getRoute(__FUNCTION__, ['categoryId' => $categoryId]),
            ['category_locale' => $sourceLocale]
        );
    }
}
