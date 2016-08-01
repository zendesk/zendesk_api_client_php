<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\MultipartUpload;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * The Apps class exposes app management methods
 *
 * @method AppLocations locations()
 */
class Apps extends ResourceAbstract
{
    use InstantiatorTrait;

    use Find;
    use Delete;
    use MultipartUpload;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'upload'       => "{$this->resourceName}/uploads.json",
            'jobStatus'    => "{$this->resourceName}/job_statuses/{id}.json",
            'create'       => "{$this->resourceName}.json",
            'update'       => "{$this->resourceName}/{id}.json",
            'findAllOwned' => "{$this->resourceName}/owned.json",
            'install'      => "{$this->resourceName}/installations.json",
            'notify'       => "{$this->resourceName}/notify.json",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadName()
    {
        return 'uploaded_data';
    }

    /**
     * {$@inheritdoc}
     */
    public function getUploadRequestMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources()
    {
        return [
            'installationLocations' => AppInstallationLocations::class,
            'locations'             => AppLocations::class,
        ];
    }

    /**
     * Create an app
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return \stdClass | null
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
     * @return \stdClass | null
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
     * @return \stdClass | null
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
     * @return \stdClass | null
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
     * @return \stdClass | null
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
     * @return \stdClass | null
     */
    public function install(array $params)
    {
        return $this->client->post($this->getRoute(__FUNCTION__), $params);
    }
}
