<?php

namespace Zendesk\API\Resources\Voice;

use Zendesk\API\Traits\Resource\Defaults;

class PhoneNumbers extends ResourceAbstract
{
    const OBJ_NAME = 'phone_number';
    const OBJ_NAME_PLURAL = 'phone_numbers';

    use Defaults;

    /**
     * @inheritdoc
     */
    protected function setUpRoutes()
    {
        $this->setRoute('search', "{$this->getResourceName()}/search.json");
    }

    /**
     * Search for available phone numbers.
     *
     * @param array $queryParams
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function search(array $queryParams = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $queryParams);
    }
}
