<?php

namespace Zendesk\API\Resources\HelpCenter;

use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\Localize;

/**
 * Class Categories
 * https://developer.zendesk.com/rest_api/docs/help_center/categories
 */
class Categories extends ResourceAbstract
{
    use Defaults;
    use Localize;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'category';

    /**
     * @var locale
     */
    private $locale;

    /**
     * @inheritdoc
     */
    protected function setUpRoutes()
    {
        $this->setRoute('updateSourceLocale', "{$this->resourceName}/{categoryId}/source_locale.json");
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
