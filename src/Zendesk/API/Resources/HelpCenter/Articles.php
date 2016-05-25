<?php

namespace Zendesk\API\Resources\HelpCenter;

use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\Locales;

/**
 * Class Articles
 * https://developer.zendesk.com/rest_api/docs/help_center/articles
 */
class Articles extends ResourceAbstract
{
    use Defaults;
    use Locales;

    /**
     * @{inheritdoc}
     */
    protected $objectName = 'article';

    /**
     * @{inheritdoc}
     */
    protected function setupRoutes()
    {
        parent::setUpRoutes();
        $this->setRoutes([
            'bulkAttach'            =>  "$this->resourceName/{articleId}/bulk_attachments.json",
            'updateSourceLocale'    =>  "$this->resourceName/{articleId}/source_locale.json",
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
}
