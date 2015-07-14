<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

class OAuthTokens extends ResourceAbstract
{
    const OBJ_NAME = 'token';
    const OBJ_NAME_PLURAL = 'tokens';

    use FindAll;
    use Find;
    use Delete;

    /**
     * @var string
     */
    protected $resourceName = 'oauth/tokens';

    protected function setUpRoutes()
    {
        $this->setRoute('current', "$this->resourceName/current.json");
    }

    /**
     * Wrapper for `delete`, called `revoke` in the API docs.
     *
     * @param null $id
     *
     * @return bool
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function revoke($id = null)
    {
        return $this->delete($id, 'delete');
    }

    /**
     * Shows the current token
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function current()
    {
        return $this->client->get($this->getRoute(__FUNCTION__));
    }
}
