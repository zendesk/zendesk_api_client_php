<?php

namespace Zendesk\API\UnitTests;

use Exception;
use Faker\Factory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\Http;
use Zendesk\API\HttpClient;

class HttpTest extends BasicTest
{
    /**
     * Test that original request exception is preserved.
     *
     * @expectedException Zendesk\API\Exceptions\ApiResponseException
     */
    public function testOriginalRequestExceptionIsPreserved()
    {
        $faker = Factory::create();

        $statusCode = $faker->numberBetween(100, 599);
        $request = new Request('GET', '/');
        $response = new Response($statusCode);
        $exceptionMessage = $faker->sentence;
        $exception = new RequestException($exceptionMessage, $request, $response);

        $guzzleClient = $this->getMockBuilder(GuzzleClient::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $guzzleClient->expects($this->once())
            ->method('send')
            ->will($this->throwException($exception));

        $zendeskClient = $this->getMockBuilder(HttpClient::class)
                ->setConstructorArgs([
                    $faker->domainWord,
                    $faker->userName,
                    $faker->randomElement([
                        'https',
                        'http',
                    ]),
                    $faker->domainName,
                    $faker->numberBetween(1),
                    $guzzleClient,
                ])
                ->getMock();

        $zendeskClient->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue([]));

        try {
            Http::send($zendeskClient, '/');
        } catch (Exception $e) {
            $originalException = $e->getPrevious()->getPrevious();
            $this->assertNotNull($originalException);
            $this->assertEquals($originalException->getMessage(), $exceptionMessage);

            throw $e;
        }
    }
}
