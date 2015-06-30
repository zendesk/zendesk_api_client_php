<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Http;

/**
 * The UserFields class exposes fields on the user profile page
 */
class UserFields extends ResourceAbstract
{
    const OBJ_NAME = 'user_field';
    const OBJ_NAME_PLURAL = 'user_fields';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoute('reorder', "{$this->resourceName}/reorder.json");
    }

    /**
     * Reorder user fields
     *
     * @param array $params
     *
     * @return mixed
     */
    public function reorder(array $params)
    {
        $postFields = ['user_field_ids' => $params];

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['postFields' => $postFields, 'method' => 'PUT']
        );

        $this->client->setSideload(null);

        return $response;
    }
}
