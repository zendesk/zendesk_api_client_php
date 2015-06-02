<?php

namespace Zendesk\API;

/**
 * The PushNotificationDevices class is a wrapper for methods as detailed on https://developer.zendesk.com/rest_api/docs/core/push_notification_devices
 * @package Zendesk\API
 *
 */
class PushNotificationDevices extends ClientAbstract
{

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Unregister the mobile devices that are receiving push notifications
     *
     * @param array $devices
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function delete(array $devices = array())
    {
        $endPoint = Http::prepare('push_notification_devices/destroy_many.json');
        $response = Http::send($this->client, $endPoint, array("push_notification_devices" => $devices), 'POST');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return true;
    }
}
