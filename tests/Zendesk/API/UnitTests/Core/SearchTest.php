<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class SearchTest
 */
class SearchTest extends BasicTest
{
    /**
     * Test seach using a querystring
     *
     * @dataProvider basicQueryStrings
     */
    public function testSearchQueryString($searchString)
    {

        $queryParams = ['sort_by' => 'updated_at'];
        $this->assertEndpointCalled(
            function () use ($searchString, $queryParams) {
                $this->client->search()->find($searchString, $queryParams);
            },
            'search.json',
            'GET',
            [
                'queryParams' => [
                    'sort_by' => 'updated_at',
                    // replace colons, the colons are a special case in this endpoint so let's do the replacement
                    // for this test only
                    'query'   => str_replace('%3A', ':', rawurlencode($searchString))
                ]
            ]
        );
    }

    /**
     * @return array
     */
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
