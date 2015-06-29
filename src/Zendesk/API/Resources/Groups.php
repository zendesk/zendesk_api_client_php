<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Http;
use Zendesk\API\UtilityTraits\InstantiatorTrait;

class Groups extends ResourceAbstract
{
    const OBJ_NAME = 'group';
    const OBJ_NAME_PLURAL = 'groups';

    use InstantiatorTrait;

    protected function setUpRoutes()
    {
        parent::setUpRoutes();
        $this->setRoute('assignable', 'groups/assignable.json');
    }

    /**
     * Show assignable groups
     *
     * @return mixed
     */
    public function assignable()
    {
        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__)
        );
        $this->client->setSideload(null);

        return $response;
    }
}
