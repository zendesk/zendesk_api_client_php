<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The AppInstallations class exposes methods seen at
 * https://developer.zendesk.com/rest_api/docs/core/apps#list-app-installations
 */
class AppInstallations extends ResourceAbstract
{
    use Defaults;

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
            'jobStatuses'  => $this->resourceName . '/job_statuses/{job_id}.json',
            'requirements' => $this->resourceName . '/{id}/requirements.json',
        ]);
    }

    /**
     * Queries the requirements installation job status using a job id given from the installation step.
     *
     * @param $jobId
     *
     * @return mixed
     */
    public function jobStatuses($jobId)
    {
        return $this->client->get($this->getRoute(__FUNCTION__, ['job_id' => $jobId]));
    }

    /**
     * Lists all Apps Requirements for an installation.
     *
     * @param null  $appInstallationId
     * @param array $params
     *
     * @return mixed
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function requirements($appInstallationId = null, array $params = [])
    {
        return $this->find($appInstallationId, $params, __FUNCTION__);
    }
}
