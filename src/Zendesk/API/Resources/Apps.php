<?php

namespace Zendesk\API\Resources;

use GuzzleHttp\Psr7\LazyOpenStream;
use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;

/**
 * The Apps class exposes app management methods
 */
class Apps extends ResourceAbstract
{
    const OBJ_NAME = 'app';
    const OBJ_NAME_PLURAL = 'apps';

    use Find;
    use Delete;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'upload'               => "{$this->resourceName}/uploads.json",
            'jobStatus'            => "{$this->resourceName}/job_statuses/{id}.json",
            'create'               => "{$this->resourceName}.json",
            'update'               => "{$this->resourceName}/{id}.json",
            'findAllOwned'         => "{$this->resourceName}/owned.json",
            'findAllInstallations' => "{$this->resourceName}/installations.json",
            'findInstallation'     => "{$this->resourceName}/installations/{id}.json",
            'install'              => "{$this->resourceName}/installations.json",
            'updateInstallation'   => "{$this->resourceName}/installations/{id}.json",
            'deleteInstallation'   => "{$this->resourceName}/installations/{id}.json",
            'notify'               => "{$this->resourceName}/notify.json",
        ]);
    }

    /**
     * Uploads an app - see http://developer.zendesk.com/documentation/rest_api/apps.html for workflow
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function upload(array $params)
    {
        if (! $this->hasKeys($params, ['file'])) {
            throw new MissingParametersException(__METHOD__, ['file']);
        }

        if (! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
                'method'    => 'POST',
                'multipart' => [
                    [
                        'name'     => 'uploaded_data',
                        'contents' => new LazyOpenStream($params['file'], 'r'),
                        'filename' => $params['file']
                    ]
                ],
            ]
        );

        return $response;
    }

    /**
     * Create an app
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params)
    {
        return $this->client->post(
            $this->getRoute(__FUNCTION__),
            $params
        );
    }

    /**
     * Queries the application build job status using a job id given from the job creation step.
     *
     * @param array $params
     *
     * @return mixed
     * @throws MissingParametersException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function jobStatus(array $params)
    {
        if (! isset($params['id'])) {
            $params = $this->addChainedParametersToParams(['id'], ['id' => self::class]);
        }

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $route = $this->getRoute(__FUNCTION__, ['id' => $params['id']]);

        return $this->client->get($route, $params);
    }

    /**
     * Update an app
     *
     * @param null  $id
     * @param array $params
     *
     * @return mixed
     * @throws MissingParametersException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function update($id = null, array $params = [])
    {
        if (empty($id)) {
            if (isset($params['id'])) {
                $id = $params['id'];
            } else {
                $id = $this->getChainedParameter(self::class, null);
            }
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->client->put(
            $this->getRoute(__FUNCTION__, ['id' => $id]),
            $params
        );
    }

    /**
     * Lists apps owned by the current account.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function findAllOwned(array $params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * The notify endpoint allows you to send messages to currently-open instances of an app.
     * For example, you could send a message to all logged-in agents telling them to take the day off.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function notify(array $params)
    {
        return $this->client->post($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * Installs an App on the account. app_id is required, as is a settings hash containing keys for all required
     * parameters for the app.
     * Any values in settings that don't correspond to a parameter that the app declares will be silently ignored.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function install(array $params)
    {
        return $this->client->post($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * Lists all App installations on the account.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function findAllInstallations(array $params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * Retrieve information about an App installation, including the settings for that App installation.
     *
     * @param       $id
     * @param array $queryParams
     *
     * @return mixed
     * @throws \Zendesk\API\Exceptions\RouteException
     *
     */
    public function findInstallation($id, array $queryParams = [])
    {
        return $this->find($id, $queryParams, __FUNCTION__);
    }

    /**
     * Retrieve information about an App installation, including the settings for that App installation.
     *
     * @param int|null $id
     * @param array    $params
     *
     * @return mixed
     * @throws MissingParametersException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function updateInstallation($id = null, array $params = [])
    {
        if (empty($id)) {
            if (isset($params['id'])) {
                $id = $params['id'];
            } else {
                $id = $this->getChainedParameter(self::class, null);
            }
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->client->put(
            $this->getRoute(__FUNCTION__, ['id' => $id]),
            $params
        );
    }

    /**
     * Removed an installed App. Use the installation id from the installation list response to make this request.
     *
     * @param int|null $id
     *
     * @return mixed
     * @throws MissingParametersException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function deleteInstallation($id = null)
    {
        $this->delete($id, __FUNCTION__);
    }
}
