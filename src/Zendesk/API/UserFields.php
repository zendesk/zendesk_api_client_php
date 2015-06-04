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
        $this->endpoint = 'user_fields/reorder.json';
        $response = Http::send_with_options($this->client, $this->endpoint,
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
