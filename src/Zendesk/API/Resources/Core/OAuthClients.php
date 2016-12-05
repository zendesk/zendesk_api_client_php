<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * Class OAuthClients
 * https://developer.zendesk.com/rest_api/docs/core/oauth_clients
 */
class OAuthClients extends ResourceAbstract
{
    use Defaults;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'client';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'clients';

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
     *
     * @return \stdClass | null
     */
    public function findAllMine(array $params = [])
    {
        return $this->findAll($params, __FUNCTION__);
    }
}
