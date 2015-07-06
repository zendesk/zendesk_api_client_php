<?php

namespace Zendesk\API\Resources;

/**
 * The Activities class exposes methods for retrieving activities
 * https://developer.zendesk.com/rest_api/docs/core/activity_stream
 */
class Activities extends ResourceAbstract
{
    const OBJ_NAME = 'activity';
    const OBJ_NAME_PLURAL = 'activities';

    /**
     * Sets up the available routes for the resource.
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'findAll' => "{$this->resourceName}.json",
            'find'    => "{$this->resourceName}/{id}.json",
        ]);
    }
}
