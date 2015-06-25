<?php

namespace Zendesk\API\Resources;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\MultipartStream;
use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;
use Zendesk\API\UtilityTraits\InstantiatorTrait;

/**
 * The Users class exposes user management methods
 * Note: you must authenticate as a user!
 * @package Zendesk\API
 */
class Users extends ResourceAbstract
{
    use InstantiatorTrait;

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
          'related'            => 'users/{id}/related.json',
          'merge'              => 'users/me/merge.json',
          'search'             => 'users/search.json',
          'autocomplete'       => 'users/autocomplete.json',
          'setPassword'        => 'users/{id}/password.json',
          'changePassword'     => 'users/{id}/password.json',
          'updateMany'         => 'users/update_many.json',
          'createMany'         => 'users/create_many.json',
          'updateProfileImage' => 'users/{id}.json',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getValidRelations()
    {
        return [
            'identities' => UserIdentities::class,
            'groups'     => Groups::class,
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

        return parent::findAll();
    }

    /**
     * Find many users
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function findMany(array $params = [])
    {
        $params         = $this->addChainedParametersToParams($params, ['ids' => get_class($this)]);
        $this->endpoint = 'users/show_many.json';

        $queryParams = ['ids' => implode(",", $params['ids'])];

        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $queryParams = array_merge($queryParams, $extraParams);

        $response = Http::sendWithOptions($this->client, $this->endpoint, ['queryParams' => $queryParams]);

        $this->client->setSideload(null);

        return $response;
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
    public function showMany(array $params = [])
    {
        if (isset($params['ids']) && isset($params['external_ids'])) {
            throw new \Exception('Only one parameter of ids or external_ids is allowed');
        } elseif (! isset($params['ids']) && ! isset($params['external_ids'])) {
            throw new \Exception('Missing parameters ids or external_ids');
        } elseif (isset($params['ids']) && is_array($params['ids'])) {
            $this->endpoint = 'users/show_many.json';
            $queryParams    = ['ids' => implode(',', $params['ids'])];
        } elseif (isset($params['external_ids']) && is_array($params['external_ids'])) {
            $this->endpoint = 'users/show_many.json';
            $queryParams    = ['external_ids' => implode(',', $params['external_ids'])];
        } else {
            throw new \Exception('Parameters ids or external_ids must be arrays');
        }

        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $queryParams = array_merge($queryParams, $extraParams);

        $response = Http::sendWithOptions(
          $this->client,
          $this->endpoint,
          ['queryParams' => $queryParams]
        );

        $this->client->setSideload(null);

        return $response;
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
    public function related(array $params = [])
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $queryParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $response    = Http::sendWithOptions(
          $this->client,
          $this->getRoute(__FUNCTION__, ['id' => $params['id']]),
          ['queryParams' => $queryParams]
        );

        $this->client->setSideload(null);

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
    public function merge(array $params = [])
    {
        $myId    = $this->getChainedParameter(get_class($this));
        $mergeMe = ! isset($myId) || is_null($myId);
        $hasKeys = $mergeMe ? array('email', 'password') : array('id');
        if ( ! $this->hasKeys($params, $hasKeys)) {
            throw new MissingParametersException(__METHOD__, $hasKeys);
        }

        $response = Http::sendWithOptions(
          $this->client,
          $this->getRoute(__FUNCTION__),
          ['postFields' => [self::OBJ_NAME => $params], 'method' => 'PUT']
        );
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Create multiple new users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function createMany(array $params)
    {
        $response = Http::sendWithOptions(
          $this->client,
          $this->getRoute(__FUNCTION__),
          ['postFields' => [self::OBJ_NAME_PLURAL => $params], 'method' => 'POST']
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Update multiple users
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */

    public function updateMany(array $params)
    {
        if (! $this->hasKeys($params, ['ids'])) {
            throw new MissingParametersException(__METHOD__, ['ids']);
        }
        $ids = $params['ids'];
        unset($params['ids']);
        $response = Http::sendWithOptions(
          $this->client,
          $this->getRoute(__FUNCTION__),
          ['postFields' => [self::OBJ_NAME => $params], 'queryParams' => ['ids' => $ids], 'method' => 'PUT']
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Update multiple individual users
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */

    public function updateManyIndividualUsers(array $params)
    {
        $this->setRoute(__METHOD__, 'users/update_many.json');
        $response = Http::sendWithOptions(
          $this->client,
          $this->getRoute(__METHOD__),
          ['postFields' => [self::OBJ_NAME_PLURAL => $params], 'method' => 'PUT']
        );
        $this->client->setSideload(null);

        return $response;
    }


    /**
     * Suspend a user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @return mixed
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
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function search(array $params)
    {
        $queryParams = isset($params['query']) ? ['query' => $params['query']] : [];
        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['queryParams' => array_merge($extraParams, $queryParams)]
        );

        $this->client->setSideload(null);

        return $response;
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
    public function autocomplete(array $params)
    {
        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__),
            ['method' => 'POST', 'queryParams' => $params]
        );

        $this->client->setSideload(null);

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
    public function updateProfileImage(array $params)
    {
        // @TODO File upload with guzzle
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (! $this->hasKeys($params, ['id', 'file'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'file']);
        }
        if (! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('users/' . $id . '.json');
        if (function_exists('curl_file_create')) {
            $response = Http::send(
                $this->client,
                $endPoint,
                $params['file'],
                'PUT',
                (isset($params['type']) ? $params['type'] : 'application/binary')
            );
        } else {
            $response = Http::send(
                $this->client,
                $endPoint,
                ['user[photo][uploaded_data]' => '@' . $params['file']],
                'PUT',
                (isset($params['type']) ? $params['type'] : 'application/binary')
            );
        }

        $this->client->setSideload(null);

        return $response;
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
     * @return mixed
     */
    public function setPassword(array $params)
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (! $this->hasKeys($params, ['id', 'password'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'password']);
        }
        $id = $params['id'];
        unset($params['id']);

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id]),
            ['postFields' => [self::OBJ_NAME => $params], 'method' => 'POST']
        );

        $this->client->setSideload(null);

        return $response;
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
    public function changePassword(array $params)
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (! $this->hasKeys($params, ['id', 'previous_password', 'password'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'previous_password', 'password']);
        }
        $id = $params['id'];
        unset($params['id']);

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id]),
            ['postFields' => $params, 'method' => 'PUT']
        );

        $this->client->setSideload(null);

        return $response;
    }
}
