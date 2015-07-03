<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class SearchTest extends BasicTest
{
    /**
     * @dataProvider basicQueryStrings
     */
    public function testSearchQueryString($searchString)
    {
        $this->mockAPIResponses(
            [
                new Response(200, [], '')
            ]
        );

        $queryParams = ['sort_by' => 'updated_at'];
        $this->client->search()->find($searchString, $queryParams);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'search.json',
                'queryParams' => ['sort_by' => 'updated_at', 'query' => rawurlencode($searchString)]
            ]
        );
    }

    public function basicQueryStrings()
    {
        return [
            [3245227],
            ['Greenbriar'],
            ['type:user "Jane Doe"'],
            ['type:ticket status:open'],
            ['type:organization created<2015-05-01'],
            ['created>2012-07-17 type:ticket organization:"MD Photo"'],
        ];
    }
}
