<?php

namespace Zendesk\API\Resources\Chat;

use Zendesk\API\Resources\ResourceAbstract;

/**
 * The Apps class exposes app management methods
 *
 * @method Apps install()
 */
class Apps extends ResourceAbstract
{
    /**
     * @var string
     */
    protected $apiBasePath = 'api/chat/';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes(
            [
            'install' => "{$this->resourceName}/installations.json",
            ]
        );
    }

    /**
     * Installs a Chat App on the account. app_id is required, as is a settings hash containing keys for all required
     * parameters for the app.
     * Any values in settings that don't correspond to a parameter that the app declares will be silently ignored.
     *
     * @param array $params
     *
     * @return \stdClass | null
     */
    public function install(array $params)
    {
        return $this->client->post($this->getRoute(__FUNCTION__), $params);
    }
}
