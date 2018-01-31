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
    use Locales {
        getRoute as protected localesGetRoute;
    }
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
            'create'                =>  "{$this->prefix}sections/{section_id}/articles.json",
            'updateSourceLocale'    =>  "$this->resourceName/{articleId}/source_locale.json",
        ]);
    }

    /**
     * Returns a route and replaces tokenized parts of the string with
     * the passed params
     *
     * @param string $name
     * @param array $params
     *
     * @return mixed The default routes, or if $name is set to `findAll`, any of the following formats
     * based on the parent chain
     * GET /api/v2/helpcenter/articles.json
     * GET /api/v2/helpcenter/sections/{section_id}/articles.json
     *
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        $lastChained = $this->getLatestChainedParameter();
        $params = $this->addChainedParametersToParams($params, [
            'section_id' => Sections::class
        ]);
        if (empty($lastChained) || $name !== 'findAll') {
            return $this->localesGetRoute($name, $params);
        } else {
            $chainedResourceName = array_keys($lastChained)[0];
            $id = $lastChained[$chainedResourceName];
            if ($chainedResourceName === Sections::class) {
                $locales = $this->getLocale();
                if ($locales) {
                    return "{$this->prefix}{$locales}/sections/$id/articles.json";
                } else {
                    return "{$this->prefix}sections/$id/articles.json";
                }
            } else {
                return $this->localesGetRoute($name, $params);
            }
        }
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
