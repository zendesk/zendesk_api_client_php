<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Defaults;

/**
 * Class OAuthClients
 * https://developer.zendesk.com/rest_api/docs/core/oauth_clients
 */
class OAuthClients extends ResourceAbstract
{
    const OBJ_NAME = 'client';
    const OBJ_NAME_PLURAL = 'clients';

    use Defaults;

    /**
     * Sets up the available routes for the resource.
     */
    protected function setUpRoutes()
    {
        $this->setRoute('findAllMine', 'users/me/oauth/clients.json');
    }

    /**
     * Find all oauth clients belonging to the logged in user.
     *
     * @param array $params
     */
    public function findAllMine(array $params = [])
    {
        $this->findAll($params, __FUNCTION__);
    }
}
