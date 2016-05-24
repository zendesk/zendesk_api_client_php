<?php

namespace Zendesk\API\Resources\HelpCenter;

use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\Localize;

/**
 * Class Articles
 * https://developer.zendesk.com/rest_api/docs/help_center/articles
 */
class Articles extends ResourceAbstract
{
    use Defaults;
    use Localize;

    /**
     * @{inheritdoc}
     */
    protected $objectName = 'article';

    /**
     * @var locale
     */
    private $locale;

    /**
     * @{inheritdoc}
     */
    protected function setupRoutes()
    {
        parent::setUpRoutes();
        $this->setRoutes([
            'bulkAttach'            =>  "$this->resourceName/{articleId}/bulk_attachments.json",
            'updateSourceLocale'    =>  "$this->resourceName/{articleId}/source_locale.json"
        ]);
    }

    /**
     * Bulk upload attachments to a specified article
     *
     * @param $articleId    The article to update
     * @param $params       An array of attachment ids
     * @throws \Exception
     */
    public function bulkAttach($articleId, array $params, $routeKey = __FUNCTION__)
    {
        try {
            $route = $this->getRoute($routeKey, ['articleId' => $articleId]);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '.json';
            $this->setRoute(__FUNCTION__, $route);
        }
        return $this->client->post(
            $route,
            ['attachement_ids' => $params]
        );
    }


    /**
     * Updates an article's source_locale property
     *
     * @param $articleId   The article to update
     * @param $sourceLocale The new source_locale
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function updateSourceLocale($articleId, $sourceLocale)
    {
        if (empty($articleId)) {
            $articleId = $this->getChainedParameter(get_class($this));
        }

        return $this->client->put(
            $this->getRoute(__FUNCTION__, ['articleId' => $articleId]),
            ['article_locale' => $sourceLocale]
        );
    }
}
