<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * PushNotificationDevices test class
 */
class PushNotificationDevicesTest extends BasicTest
{

    public function testDelete()
    {
        $devices = array("token1", "token2", "token3");
        $deleted = $this->client->push_notification_devices()->delete($devices);
        $this->assertEquals(true, $deleted, 'Returns true on success');
    }

}
