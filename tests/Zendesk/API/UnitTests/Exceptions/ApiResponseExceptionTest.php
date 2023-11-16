<?php

namespace Zendesk\API\UnitTests\Exceptions;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Exceptions\ApiResponseException;

class ApiResponseExceptionTest extends BasicTest
{
    public function testServerException()
    {
        $request = new Request("GET","");
        $response = new Response(200, ["Content-Type"=> "application/json"],"");
        $requestException = new ServerException("test", $request, $response);

        $subject = new ApiResponseException($requestException);

        $this->assertEquals([], $subject->getErrorDetails());
    }
}
