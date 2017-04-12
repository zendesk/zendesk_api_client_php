<?php

namespace Zendesk\API\UnitTests;

use Exception;
use Faker\Factory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Zendesk\API\Http;
use Zendesk\API\HttpClient;

class HttpTest extends BasicTest
{
    /**
     * Test that original request exception is preserved.
     */
    public function testOriginalRequestExceptionIsPreserved()
    {
        $faker = Factory::create();

        $exceptionMessage = $faker->sentence;
        $exception = $this->mockRequestException($exceptionMessage);

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
        }
    }

    /**
     * Create a mocked RequestExcpetion
     *
     * @param string $message
     *
     * @return RequestException
     */
    private function mockRequestException($message)
    {
        $request = $this->getMockBuilder(Request::class)
                 ->disableOriginalConstructor()
                 ->getMock();
        $response = $this->getMockBuilder(Response::class)
                  ->disableOriginalConstructor()
                  ->getMock();
        $body = $this->getMockBuilder(Stream::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $request->method('getBody')
            ->will($this->returnValue($body));
        $response->method('getBody')
            ->will($this->returnValue($body));

        return new RequestException($message, $request, $response);
    }
}
