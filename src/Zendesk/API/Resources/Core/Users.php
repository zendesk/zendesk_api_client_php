<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\CreateMany;
use Zendesk\API\Traits\Resource\CreateOrUpdateMany;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\FindMany;
use Zendesk\API\Traits\Resource\MultipartUpload;
use Zendesk\API\Traits\Resource\UpdateMany;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * The Users class exposes user management methods
 * Note: you must authenticate as a user!
 *
 * @method Groups groups()
 * @method UserIdentities identities()
 * @method Organizations organizations()
 * @method OrganizationMemberships organizationMemberships()
 * @method OrganizationSubscriptions organizationSubscriptions()
 * @method Requests requests()
 */
class Users extends ResourceAbstract
{
    use InstantiatorTrait;

    use Defaults {
        findAll as traitFindAll;
    }
    use MultipartUpload;

    use CreateMany;
    use FindMany {
        findMany as traitFindMany;
    }
    use UpdateMany;
    use CreateOrUpdateMany;

    /**
     * @var UserIdentities
     */
    protected $identities;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'related'                    => 'users/{id}/related.json',
            'merge'                      => 'users/{id}/merge.json',
            'search'                     => 'users/search.json',
            'autocomplete'               => 'users/autocomplete.json',
            'setPassword'                => 'users/{id}/password.json',
            'changePassword'             => 'users/{id}/password.json',
            'updateMany'                 => 'users/update_many.json',
            'createOrUpdate'             => 'users/create_or_update.json',
            'createOrUpdateMany'         => 'users/create_or_update_many.json',
            'createMany'                 => 'users/create_many.json',
            'updateProfileImageFromFile' => 'users/{id}.json',
            'updateProfileImageFromUrl'  => 'users/{id}.json',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources()
    {
        return [
            'groupMemberships'          => GroupMemberships::class,
            'groups'                    => Groups::class,
            'identities'                => UserIdentities::class,
            'organizations'             => Organizations::class,
            'organizationMemberships'   => OrganizationMemberships::class,
            'organizationSubscriptions' => OrganizationSubscriptions::class,
            'requests'                  => Requests::class,
            'sessions'                  => Sessions::class,
            'tickets'                   => UserTickets::class,
        ];
    }

    /**
     * List all users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function findAll(array $params = [])
    {
        if (isset($params['organization_id'])) {
            $this->endpoint = "organizations/{$params['organization_id']}/users.json";
        } elseif (isset($params['group_id'])) {
            $this->endpoint = 'groups/' . $params['group_id'] . '/users.json';
        } else {
            $this->endpoint = 'users.json';
        }

        return $this->traitFindAll($params);
    }

    /**
     * Find users by ids or external_ids
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function findMany(array $params = [])
    {
        if (isset($params['ids']) xor isset($params['external_ids'])) {
            if (isset($params['ids'])) {
                $key = 'ids';
                $ids = $params['ids'];
            } elseif (isset($params['external_ids'])) {
                $key = 'external_ids';
                $ids = $params['external_ids'];
            }
        } else {
            throw new \Exception('Missing parameters ids or external_ids');
        }

        return $this->traitFindMany($ids, [], $key);
    }

    /**
     * Get related information about the user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function related(array $params = [])
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $params['id']]),
            ['queryParams' => $params]
        );

        return $response;
    }

    /**
     * Merge user (by default yourself) with the specified user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function merge(array $params = [])
    {
        $id = $this->getChainedParameter(get_class($this));
        $mergeMe = ($id === null || $id === 'me');
        $hasKeys = $mergeMe ? ['email', 'password'] : ['id'];

        if (! $this->hasKeys($params, $hasKeys)) {
            throw new MissingParametersException(__METHOD__, $hasKeys);
        }

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__, [
                'id' => $mergeMe ? 'me' : $id,
            ]),
            ['postFields' => [$this->objectName => $params], 'method' => 'PUT']
        );

        return $response;
    }

    /**
     * Update multiple users
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @return \stdClass | null
     */
    public function suspend(array $params = [])
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }
        $params['suspended'] = true;

        return $this->update($params['id'], $params);
    }

    /**
     * Search for users
     *
     * @param array $params Accepts `external_id` & `query`
     *
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function search(array $params)
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * Requests autocomplete for users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function autocomplete(array $params)
    {
        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['method' => 'POST', 'queryParams' => $params]
        );

        return $response;
    }

    /**
     * {$@inheritdoc}
     *
     * @return String
     */
    public function getUploadName()
    {
        return 'user[photo][uploaded_data]';
    }

    /**
     * {$@inheritdoc}
     *
     * @return String
     */
    public function getUploadRequestMethod()
    {
        return 'PUT';
    }

    /**
     * Update a user's profile image
     *
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function updateProfileImageFromFile(array $params)
    {
        $this->setAdditionalRouteParams(['id' => $this->getChainedParameter(self::class)]);

        return $this->upload($params, __FUNCTION__);
    }

    /**
     * Update a user's profile image
     *
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function updateProfileImageFromUrl(array $params)
    {
        if (! isset($params['id']) || empty($params['id'])) {
            $params = $this->addChainedParametersToParams($params, ['id' => self::class]);
        }

        if (! $this->hasKeys($params, ['id', 'url'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'url']);
        }

        $endpoint = $this->getRoute(__FUNCTION__, ['id' => $params['id']]);

        $putData = [
            $this->objectName => [
                'remote_photo_url' => $params['url']
            ]
        ];

        return $this->client->put($endpoint, $putData);
    }

    /**
     * Show the current user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @return \stdClass | null
     */
    public function me(array $params = [])
    {
        $params['id'] = 'me';

        return $this->find($params['id']);
    }

    /**
     * Sets a user's initial password
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return null
     */
    public function setPassword(array $params)
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (! $this->hasKeys($params, ['id', 'password'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'password']);
        }
        $id = $params['id'];
        unset($params['id']);

        return $this->client->post($this->getRoute(__FUNCTION__, ['id' => $id]), $params);
    }

    /**
     * Change a user's password
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return null
     */
    public function changePassword(array $params)
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (! $this->hasKeys($params, ['id', 'previous_password', 'password'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'previous_password', 'password']);
        }
        $id = $params['id'];
        unset($params['id']);

        return $this->client->put($this->getRoute(__FUNCTION__, ['id' => $id]), $params);
    }

    /**
     * Create or updates a user
     *
     * @param array  $params
     *
     * @param string $routeKey
     * @return null|\stdClass
     */
    public function createOrUpdate(array $params, $routeKey = __FUNCTION__)
    {
        $route = $this->getRoute($routeKey, $params);
        return $this->client->post(
            $route,
            [$this->objectName => $params]
        );
    }
}
