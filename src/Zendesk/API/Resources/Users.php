<?php

namespace Zendesk\API\Resources;

use GuzzleHttp\Psr7\LazyOpenStream;
use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;
use Zendesk\API\Traits\Resource\Create;
use Zendesk\API\Traits\Resource\CreateMany;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;
use Zendesk\API\Traits\Resource\FindMany;
use Zendesk\API\Traits\Resource\Update;
use Zendesk\API\Traits\Resource\UpdateMany;
use Zendesk\API\UtilityTraits\InstantiatorTrait;

/**
 * The Users class exposes user management methods
 * Note: you must authenticate as a user!
 *
 * @package Zendesk\API
 */
class Users extends ResourceAbstract
{
    use InstantiatorTrait;

    use FindAll {
        findAll as traitFindall;
    }

    use Find;
    use Create;
    use Update;
    use Delete;

    use FindMany {
        findMany as traitFindMany;
    }
    use CreateMany;
    use UpdateMany;

    const OBJ_NAME = 'user';
    const OBJ_NAME_PLURAL = 'users';

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
            'merge'                      => 'users/me/merge.json',
            'search'                     => 'users/search.json',
            'autocomplete'               => 'users/autocomplete.json',
            'setPassword'                => 'users/{id}/password.json',
            'changePassword'             => 'users/{id}/password.json',
            'updateMany'                 => 'users/update_many.json',
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
            'identities'                => UserIdentities::class,
            'groups'                    => Groups::class,
            'organizations'             => Organizations::class,
            'organizationMemberships'   => OrganizationMemberships::class,
            'organizationSubscriptions' => OrganizationSubscriptions::class,
        ];
    }

    /**
     * List all users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
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

        return $this->traitFindall();
    }

    /**
     * Find users by ids or external_ids
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
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
     * @return mixed
     */
    public function related(
        array $params = []
    ) {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $queryParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $response    = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $params['id']]),
            ['queryParams' => $queryParams]
        );

        return $response;
    }

    /**
     * Merge the specified user (???)
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function merge(
        array $params = []
    ) {
        $myId    = $this->getChainedParameter(get_class($this));
        $mergeMe = ! isset($myId) || is_null($myId);
        $hasKeys = $mergeMe ? ['email', 'password'] : ['id'];
        if (! $this->hasKeys($params, $hasKeys)) {
            throw new MissingParametersException(__METHOD__, $hasKeys);
        }

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['postFields' => [self::OBJ_NAME => $params], 'method' => 'PUT']
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
     * @return mixed
     */
    public function suspend(
        array $params = []
    ) {
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
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function search(
        array $params
    ) {
        $queryParams = isset($params['query']) ? ['query' => $params['query']] : [];
        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);

        return $this->client->get($this->getRoute(__FUNCTION__), array_merge($extraParams, $queryParams));
    }

    /**
     * Requests autocomplete for users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function autocomplete(
        array $params
    ) {
        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['method' => 'POST', 'queryParams' => $params]
        );

        return $response;
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
     * @return mixed
     */
    public function updateProfileImageFromFile(
        array $params
    ) {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (! $this->hasKeys($params, ['id', 'file'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'file']);
        }

        if (! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }

        $id = $params['id'];
        unset($params['id']);

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id]),
            [
                'method'    => 'PUT',
                'multipart' => [
                    [
                        'name'     => 'user[photo][uploaded_data]',
                        'contents' => new LazyOpenStream($params['file'], 'r'),
                        'filename' => $params['file']
                    ]
                ],
            ]
        );

        return $response;
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
     * @return mixed
     */
    public function updateProfileImageFromUrl(
        array $params
    ) {
        if (! isset($params['id']) || empty($params['id'])) {
            $params = $this->addChainedParametersToParams($params, ['id' => self::class]);
        }

        if (! $this->hasKeys($params, ['id', 'url'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'url']);
        }

        $endpoint = $this->getRoute(__FUNCTION__, ['id' => $params['id']]);

        $putData = [
            self::OBJ_NAME => [
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
     * @return mixed
     */
    public function me(
        array $params = []
    ) {
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
     * @return mixed
     */
    public function setPassword(
        array $params
    ) {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (! $this->hasKeys($params, ['id', 'password'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'password']);
        }
        $id = $params['id'];
        unset($params['id']);

        return $this->client->post($this->getRoute(__FUNCTION__, ['id' => $id]), [self::OBJ_NAME => $params]);
    }

    /**
     * Change a user's password
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function changePassword(
        array $params
    ) {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (! $this->hasKeys($params, ['id', 'previous_password', 'password'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'previous_password', 'password']);
        }
        $id = $params['id'];
        unset($params['id']);

        return $this->client->put($this->getRoute(__FUNCTION__, ['id' => $id]), $params);

    }
}
