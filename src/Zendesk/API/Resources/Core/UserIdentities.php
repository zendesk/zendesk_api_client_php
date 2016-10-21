<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The UserIdentities class exposes fields on the user profile page
 */
class UserIdentities extends ResourceAbstract
{
    use Defaults {
        findAll as traitFindAll;
        find as traitFind;
        create as traitCreate;
        update as traitUpdate;
        delete as traitDelete;
    }

    protected $resourceName = 'identities';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'findAll'             => "users/{userId}/{$this->resourceName}.json",
            'find'                => "users/{userId}/{$this->resourceName}/{id}.json",
            'update'              => "users/{userId}/{$this->resourceName}/{id}.json",
            'makePrimary'         => "users/{userId}/{$this->resourceName}/{id}/make_primary.json",
            'verify'              => "users/{userId}/{$this->resourceName}/{id}/verify.json",
            'requestVerification' => "users/{userId}/{$this->resourceName}/{id}/request_verification.json",
            'delete'              => "users/{userId}/{$this->resourceName}/{id}.json",
            'create'              => "users/{userId}/{$this->resourceName}.json",
            'createAsEndUser'     => "end_users/{userId}/{$this->resourceName}.json",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(array $params = [])
    {
        $this->addUserIdToRouteParams($params);

        return $this->traitFindAll($params);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id = null, array $queryParams = [])
    {
        $this->addUserIdToRouteParams($queryParams);

        return $this->traitFind($id, $queryParams);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $params = [])
    {
        $this->addUserIdToRouteParams($params);

        return $this->traitCreate($params);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id = null, array $updateResourceFields = [])
    {
        $this->addUserIdToRouteParams($updateResourceFields);

        return $this->traitUpdate($id, $updateResourceFields);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id = null)
    {
        $this->addUserIdToRouteParams([]);

        return $this->traitDelete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function createAsEndUser(array $params = [])
    {
        $this->addUserIdToRouteParams($params);

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
                'postFields' => [$this->objectName => $params],
                'method'     => 'POST'
            ]
        );

        return $response;
    }

    /**
     * This API method allows you to set an identity to primary.
     *
     * @param array $params
     * @return null|\stdClass
     */
    public function makePrimary(array $params = [])
    {
        return $this->makePutRequest(__FUNCTION__, $params);
    }

    /**
     * This API method only allows you to set an identity as verified. This is allowed only for agents.
     *
     * @param array $params
     * @return null|\stdClass
     */
    public function verify(array $params = [])
    {
        return $this->makePutRequest(__FUNCTION__, $params);
    }

    /**
     * This sends a verification email to the user, asking him to click a link in order to
     * verify ownership of the email address
     *
     * @param array $params
     * @return null|\stdClass
     */
    public function requestVerification(array $params = [])
    {
        return $this->makePutRequest(__FUNCTION__, $params);
    }

    /**
     * Get the userId passed as a parameter or as a chained parameter
     *
     * @param array $params
     *
     * @throws MissingParametersException
     */
    private function addUserIdToRouteParams(array $params)
    {
        if (isset($params['userId'])) {
            $userId = $params['userId'];
        } else {
            $userId = $this->getChainedParameter(Users::class);
        }

        if (empty($userId)) {
            throw new MissingParametersException(__METHOD__, ['userId']);
        }

        $this->setAdditionalRouteParams(['userId' => $userId]);
    }

    /**
     * This makes a `PUT` request to the endpoint defined by the $callingMethod parameter.
     *
     * @param string $callingMethod
     * @param array  $params
     *
     * @return \stdClass | null
     * @throws MissingParametersException
     * @throws \Exception
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    private function makePutRequest($callingMethod, $params = [])
    {
        $this->addUserIdToRouteParams($params);

        if (isset($params['id'])) {
            $id = $params['id'];
        } else {
            $id = $this->getChainedParameter(self::class);
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $response = Http::send(
            $this->client,
            $this->getRoute($callingMethod, ['id' => $id]),
            ['method' => 'PUT']
        );

        return $response;
    }
}
