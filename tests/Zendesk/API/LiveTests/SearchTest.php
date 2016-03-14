<?php

namespace Zendesk\API\LiveTests;

class SearchTest extends BasicTest
{
    /**
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testSearchQueryString()
    {
        $response = $this->client->search()->find('type:ticket status:open', ['sort_by' => 'updated_at']);

        $this->assertTrue(isset($response->results), 'Should contain a property called `results`');
        $this->assertTrue(
            is_array($response->results) && count($response->results) > 0,
            'Should contain a non-empty `results` array.'
        );
    }
}
