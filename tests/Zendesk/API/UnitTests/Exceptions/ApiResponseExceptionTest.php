<?php

namespace Zendesk\API\UnitTests\Exceptions;

use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Exceptions\ApiResponseException;

class ApiResponseExceptionTest extends BasicTest
{
    /**
     * Tests if previous exception was passed to ApiResponseException
     */
    public function testPreviousException()
    {
        $this->markTestSkipped('Broken in PHP 7.4 (mocking)');

        $message = 'The previous exception was not passed to ApiResponseException';
        $mockException = $this
            ->getMockBuilder('GuzzleHttp\Exception\RequestException')
            ->disableOriginalConstructor()
            ->getMock();
        $mockException->method('hasResponse')->willReturn(true);
        $apiException = new ApiResponseException($mockException);
        $previousException = $apiException->getPrevious();

        $this->assertEquals($mockException, $previousException, $message);
    }
}
