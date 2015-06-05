<?php

namespace Zendesk\API;

/**
 * The UserFields class exposes fields on the user profile page
 * @package Zendesk\API
 */
class UserFields extends ResourceAbstract
{
    protected $endpoint = 'user_fields.json';

    const OBJ_NAME = 'user_field';
    const OBJ_NAME_PLURAL = 'user_fields';

    protected function setUpRoutes()
    {
        parent::setUpRoutes();
        $this->setRoute('reorder', 'user_fields/reorder.json');
    }
    /**
     * Reorder user fields
     *
     * @param array $userFieldIds
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function reorder(array $userFieldIds)
    {
        $response = Http::send_with_options($this->client, $this->getRoute('reorder'),
            [
                'postFields' => ['user_field_ids' => $userFieldIds],
                'method'     => 'PUT'
            ]
        );

        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

}
