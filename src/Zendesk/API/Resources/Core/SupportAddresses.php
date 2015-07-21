<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

class SupportAddresses extends ResourceAbstract
{
    const OBJ_NAME = 'recipient_addresses';
    const OBJ_NAME_PLURAL = 'recipient_addresses';

    use Defaults;

    protected $resourceName = 'recipient_addresses';

    protected function setUpRoutes()
    {
        $this->setRoute('verify', "{$this->resourceName}/{id}/verify.json");
    }

    /**
     * Verify recipient address
     *
     * @param null  $recipientAddressId
     * @param array $updateFields
     *
     * @return mixed
     */
    public function verify($recipientAddressId = null, array $updateFields = [])
    {
        $class = get_class($this);
        if (empty($recipientAddressId)) {
            $recipientAddressId = $this->getChainedParameter($class);
        }

        return $this->client->put($this->getRoute(__FUNCTION__, ['id' => $recipientAddressId]), $updateFields);
    }
}
