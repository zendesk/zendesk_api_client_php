<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;

/**
 * The Autocomplete class is as per http://developer.zendesk.com/documentation/rest_api/autocomplete.html
 */
class Autocomplete extends ResourceAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoute('tags', 'autocomplete/tags.json');
    }

    /**
     * Submits a request for matching tags
     *
     * @param array $params
     *
     * @throws \Exception
     * @return \stdClass | null
     */
    public function tags(array $params)
    {
        $this->client->get($this->getRoute(__FUNCTION__), $params);
    }
}
