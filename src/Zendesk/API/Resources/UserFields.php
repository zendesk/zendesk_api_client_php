<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Http;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The UserFields class exposes fields on the user profile page
 */
class UserFields extends ResourceAbstract
{
    use Defaults;

    const OBJ_NAME = 'user_field';
    const OBJ_NAME_PLURAL = 'user_fields';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
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

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['postFields' => $postFields, 'method' => 'PUT']
        );

        return $response;
    }
}
