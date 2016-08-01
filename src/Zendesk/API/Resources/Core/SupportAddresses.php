<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * Class SupportAddresses
 * https://developer.zendesk.com/rest_api/docs/core/support_addresses
 */
class SupportAddresses extends ResourceAbstract
{
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
     * @return \stdClass | null
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
