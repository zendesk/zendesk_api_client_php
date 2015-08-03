<?php

namespace Zendesk\API\LiveTests;

class SearchTest extends BasicTest
{
    public function testSearchQueryString()
    {
        $response = $this->client->search()->find('type:ticket status:open', ['sort_by' => 'updated_at']);

        $this->assertTrue(isset($response->results), 'Should contain a property called `results`');
    }
}
