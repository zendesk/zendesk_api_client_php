<?php

namespace Zendesk\API\LiveTests;

class SearchTest extends BasicTest
{
    /**
     * @dataProvider basicQueryStrings
     */
    public function testSearchQueryString($searchString)
    {
        $queryParams = ['sort_by' => 'updated_at'];
        $response    = $this->client->search()->find($searchString, $queryParams);

        var_dump($response);
        exit;
    }

    public function basicQueryStrings()
    {
        return [
            //[3245227],
            //['Greenbriar'],
            ['type:user "Jane Doe"'],
            //['type:ticket status:open'],
            //['type:organization created<2015-05-01'],
            //['created>2012-07-17 type:ticket organization:"MD Photo"'],
        ];
    }
}
