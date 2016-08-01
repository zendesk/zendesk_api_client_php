<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The OrganizationFields class exposes methods as detailed on
 * http://developer.zendesk.com/documentation/rest_api/organization_fields.html
 */
class OrganizationFields extends ResourceAbstract
{
    use Defaults;

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
     * @return \stdClass | null
     */
    public function reorder(array $params)
    {
        if (! $this->hasKeys($params, ['organization_field_ids'])) {
            throw new MissingParametersException(__METHOD__, ['organization_field_ids']);
        }

        $putData = ['organization_field_ids' => $params['organization_field_ids']];

        $endpoint = $this->getRoute(__FUNCTION__);
        $response = $this->client->put($endpoint, $putData);

        return $response;
    }
}
