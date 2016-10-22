<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;
use Zendesk\API\Traits\Resource\Update;

/**
 * The AppInstallations class exposes methods seen at
 * https://developer.zendesk.com/rest_api/docs/core/apps#list-app-installations
 */
class AppInstallations extends ResourceAbstract
{
    use Update {
        update as TraitUpdate;
    }
    use Delete;
    use Find;
    use FindAll;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'installation';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'installations';

    /**
     * {@inheritdoc}
     */
    protected $resourceName = 'apps/installations';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'create' => $this->resourceName . '.json',
            'update' => $this->resourceName . '/{id}.json',
            'jobStatuses' => $this->resourceName . '/job_statuses/{job_id}.json',
            'requirements' => $this->resourceName . '/{id}/requirements.json',
        ]);
    }

    /**
     * Queries the requirements installation job status using a job id given from the installation step.
     *
     * @param $jobId
     *
     * @return \stdClass | null
     */
    public function jobStatuses($jobId)
    {
        return $this->client->get($this->getRoute(__FUNCTION__, ['job_id' => $jobId]));
    }

    /**
     * Lists all Apps Requirements for an installation.
     *
     * @param null $appInstallationId
     * @param array $params
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function requirements($appInstallationId = null, array $params = [])
    {
        return $this->find($appInstallationId, $params, __FUNCTION__);
    }

    /**
     * Installs an app
     *
     * @param array  $params
     *
     * @param string $routeKey
     * @return null|\stdClass
     */
    public function create(array $params, $routeKey = __FUNCTION__)
    {
        try {
            $route = $this->getRoute($routeKey, $params);
        } catch (RouteException $e) {
            if (!isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '.json';
            $this->setRoute(__FUNCTION__, $route);
        }

        return $this->client->post(
            $route,
            $params
        );
    }

    /**
     * Updates the settings for the app installation
     *
     * @param null $id
     * @param array $updateResourceFields
     * @param string $routeKey
     * @return \stdClass | null
     */
    public function update($id = null, array $updateResourceFields = [], $routeKey = __FUNCTION__)
    {
        if (empty($id)) {
            $id = $this->getChainedParameter(__CLASS__);
        }

        $route = $this->getRoute($routeKey, ['id' => $id]);

        return $this->client->put(
            $route,
            $updateResourceFields
        );
    }
}
