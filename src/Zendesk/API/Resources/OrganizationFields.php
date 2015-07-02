<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;

/**
 * The OrganizationFields class exposes methods as detailed on
 * http://developer.zendesk.com/documentation/rest_api/organization_fields.html
 */
class OrganizationFields extends ResourceAbstract
{
    const OBJ_NAME = 'organization_field';
    const OBJ_NAME_PLURAL = 'organization_fields';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoute('reorder', 'organization_fields/reorder.json');
    }

    /**
     * Reorder organization fields
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return mixed
     */
    public function reorder(array $params)
    {
        if (! $this->hasKeys($params, ['organization_field_ids'])) {
            throw new MissingParametersException(__METHOD__, ['organization_field_ids']);
        }

        $putData = ['organization_field_ids' => $params['organization_field_ids']];

        $endpoint = $this->getRoute(__FUNCTION__);
        $response = $this->client->put($endpoint, $putData);

        $this->client->setSideload(null);

        return $response;
    }
}
