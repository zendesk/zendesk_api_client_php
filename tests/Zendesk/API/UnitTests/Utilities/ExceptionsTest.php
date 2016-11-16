<?php

namespace Zendesk\API\UnitTests\Utilities;

use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Exceptions\ApiResponseException;

class ExceptionsTest extends BasicTest
{

    public function testPreviousException()
    {
        $message = 'The previous exception was not passed to ApiResponseException';
        $mockException = $this
            ->getMockBuilder('GuzzleHttp\Exception\RequestException')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $mockException->method('hasResponse')->willReturn(true);
        $apiException = new ApiResponseException($mockException);
        $previousException = $apiException->getPrevious();

        $this->assertEquals($mockException, $previousException, $message);
    }
}
