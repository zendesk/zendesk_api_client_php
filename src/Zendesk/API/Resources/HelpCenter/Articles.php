<?php

namespace Zendesk\API\Resources\HelpCenter;

use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\Locales;
use Zendesk\API\Traits\Resource\Search;

/**
 * Class Articles
 * https://developer.zendesk.com/rest_api/docs/help_center/articles
 */
class Articles extends ResourceAbstract
{
    use Defaults;
    use Locales;
    use Search;

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
     * @param int    $articleId The article to update
     * @param array  $params    An array of attachment ids
     * @param string $routeKey  The route to set
     * @return null|\stdClass
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
            ['attachment_ids' => $params]
        );
    }
}
