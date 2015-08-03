<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * PushNotificationDevices test class
 */
class PushNotificationDevicesTest extends BasicTest
{
    /**
     * Test the deleteMany method
     */
    public function testDeleteMany()
    {
        $postFields = ['tokens' => ['token1', 'token2']];
        $this->assertEndpointCalled(
            function () use ($postFields) {
                $this->client->pushNotificationDevices()->deleteMany($postFields);
            },
            'push_notification_devices/destroy_many.json',
            'POST',
            [
                'postFields' => ['push_notification_devices' => $postFields['tokens']]
            ]
        );
    }
}
