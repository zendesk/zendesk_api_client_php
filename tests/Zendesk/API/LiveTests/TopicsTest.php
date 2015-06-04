<?php

namespace Zendesk\API\LiveTests;

/**
 * Topics test class
 */
class TopicsTest extends BasicTest
{
    public function testFindMany()
    {
        $topicIds = [123, 456];
        $this->mockApiCall('GET', '/topics/show_many.json?ids=' . urlencode(implode(',', $topicIds)), []);

        $this->client->topics()->findMany(['ids' => $topicIds]);
    }

    public function testImport()
    {
        $this->mockApiCall('POST', '/import/topics.json', ['topic' => []], ['code' => 201]);
        $this->client->topics()->import([]);
    }
}
