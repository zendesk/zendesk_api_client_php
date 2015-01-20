<?php
namespace Zendesk\API\Tests;
require __DIR__ . '/../CurlRequest.php';
use Zendesk\API\Client;
use \Zendesk\API\Http;
use \Zendesk\API\CurlRequest;

/**
 * Http test class
 */
class HttpTest extends BasicTest {
    public function testPrepare() {
    }

    public function testSend() {
        $fixture = Http::send($this->client, 'tickets.json');
    }
}
