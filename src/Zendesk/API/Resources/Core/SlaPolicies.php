<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Slas class exposes methods seen at
 * https://developer.zendesk.com/rest_api/docs/core/sla_policies
 */
class SlaPolicies extends ResourceAbstract
{
    use Defaults;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'sla_policy';

    protected $resourceName = 'slas/policies';

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'replace'     => "{$this->resourceName}/{id}/replace.json",
            'reorder'     => "{$this->resourceName}/reorder.json",
            'definitions' => "{$this->resourceName}/definitions.json",
        ]);
    }

    /**
     * Replace a single SLA Policy
     *
     * The replaced SLA policy is versioned. Each time an SLA policy is updated, a new SLA policy is saved.
     * Altering the title or description of SLA policies doesn't constitute a version change.
     *
     * @param null  $id
     * @param array $updateResourceFields
     *
     * @return mixed
     */
    public function replace($id = null, $updateResourceFields = [])
    {
        return $this->update($id, $updateResourceFields, __FUNCTION__);
    }

    /**
     * Reorder SLA Policies
     *
     * @parama array  $ids
     *
     * @return mixed
     */
    public function reorder($ids = [])
    {
        return $this->client->put($this->getRoute(__FUNCTION__), ['sla_policy_ids' => $ids]);
    }

    /**
     * Retrieve supported filter definition items
     *
     * @parama array  $ids
     *
     * @return mixed
     */
    public function definitions(array $params = [])
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }
}
