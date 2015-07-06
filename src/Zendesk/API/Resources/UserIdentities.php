<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;

/**
 * The UserIdentities class exposes fields on the user profile page
 * @package Zendesk\API
 */
class UserIdentities extends ResourceAbstract
{

    const OBJ_NAME = 'identity';
    const OBJ_NAME_PLURAL = 'identities';

    protected $resourceName = 'identities';

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

        return parent::findAll($params);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id = null, array $queryParams = [])
    {
        $this->addUserIdToRouteParams($queryParams);

        return parent::find($id, $queryParams);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $params = [])
    {
        $this->addUserIdToRouteParams($params);

        return parent::create($params);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id = null, array $updateResourceFields = [])
    {
        $this->addUserIdToRouteParams($updateResourceFields);

        return parent::update($id, $updateResourceFields);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id = null)
    {
        $this->addUserIdToRouteParams([]);

        return parent::delete($id);
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
                'postFields' => [$this::OBJ_NAME => $params],
                'method'     => 'POST'
            ]
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * This API method allows you to set an identity to primary.
     */
    public function makePrimary(array $params = [])
    {
        return $this->makePutRequest(__FUNCTION__, $params);
    }

    /**
     * This API method only allows you to set an identity as verified. This is allowed only for agents.
     */
    public function verify(array $params = [])
    {
        return $this->makePutRequest(__FUNCTION__, $params);
    }

    /**
     * This sends a verification email to the user, asking him to click a link in order to
     * verify ownership of the email address
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
     * @param array $params
     *
     * @return array
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
