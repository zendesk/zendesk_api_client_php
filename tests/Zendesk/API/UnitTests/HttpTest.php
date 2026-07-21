<?php

namespace Zendesk\API\UnitTests;

use Exception;
use Faker\Factory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Zendesk\API\Exceptions\ApiResponseException;
use Zendesk\API\Http;
use Zendesk\API\HttpClient;

class HttpTest extends BasicTest
{
    public function testOriginalRequestExceptionIsPreserved()
    {
        $this->markTestSkipped('Broken in PHP 8.2 (mocking)');
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

    public function testConnectExceptionIsWrappedInApiResponseException()
    {
        $request          = new Request('GET', 'http://example.com');
        $connectException = new ConnectException('Connection refused', $request);
        $this->mockApiResponses([$connectException]);

        try {
            Http::send($this->client, '/tickets.json');
            $this->fail('Expected ApiResponseException was not thrown.');
        } catch (ApiResponseException $e) {
            $this->assertSame($connectException, $e->getPrevious()->getPrevious());
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

        if (class_exists(ResponseException::class)) {
            return new ResponseException($message, $request, $response);
        }

        return new RequestException($message, $request, $response);
    }
}
