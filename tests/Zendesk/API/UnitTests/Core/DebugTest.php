<?php

namespace Zendesk\API\UnitTests\Core;

use Faker\Factory;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\HttpClient;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Class DebugTest
 */
class DebugTest extends BasicTest
{

    /**
     * Test that Debug object can be converted to string and contains all request parameter
     */
    public function testDebugCanBeConvertedToString()
    {
        $faker = Factory::create();

        $requestDomain = $faker->domainName;
        $requestBody   = $faker->text;
        list($requestHeaderA, $requestHeaderB, $responseHeaderA, $responseHeaderB) = $faker->sentences(4);
        $responseCode = $faker->randomElement([200, 404, 500]);


        $zendeskClient = new HttpClient($requestDomain);
        $request       = new Request(
            'GET',
            'http://'.$requestDomain,
            ['REQUEST_HEADER_1' => $requestHeaderA, 'REQUEST_HEADER_2' => $requestHeaderB],
            $requestBody
        );


        $response = new Response(
            $responseCode,
            ['RESPONSE_HEADER_1' => $responseHeaderA, 'RESPONSE_HEADER_2' => $responseHeaderB]
        );

        $exception = new RequestException($faker->sentence, $request, $response);


        $zendeskClient->setDebug(
            $request->getHeaders(),
            $request->getBody(),
            $response->getStatusCode(),
            $response->getHeaders(),
            $exception
        );

        $debugText = $zendeskClient->getDebug()->__toString();
        $this->assertContains((string)$responseCode, $debugText);
        $this->assertContains($requestDomain, $debugText);
        $this->assertContains($requestBody, $debugText);

        $this->assertContains('REQUEST_HEADER_', $debugText);
        $this->assertContains($requestHeaderA, $debugText);
        $this->assertContains($requestHeaderB, $debugText);

        $this->assertContains('RESPONSE_HEADER_', $debugText);
        $this->assertContains($responseHeaderA, $debugText);
        $this->assertContains($responseHeaderB, $debugText);

        $this->assertContains($exception->getMessage(), $debugText);
    }

}
