<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Find;

/**
 * The Translations class finds the manifest files for localizations
 */

class Translations extends ResourceAbstract
{
    use Find;
    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'manifest'      => "{$this->resourceName}/manifest.json",
        ]);
    }

    /**
     * Get the manifest json file
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function manifest(array $params = [])
    {
        return $this->find(null, $params, __FUNCTION__);
    }

}