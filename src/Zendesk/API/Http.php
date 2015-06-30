<?php

namespace Zendesk\API;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Zendesk\API\Exceptions\ApiResponseException;
use Zendesk\API\Exceptions\AuthException;

/**
 * HTTP functions via curl
 * @package Zendesk\API
 */
class Http
{
    public static $curl;

    /**
     * Prepares an endpoint URL with optional side-loading
     *
     * @param array $sideload
     * @param array $iterators
     *
     * @return string
     */
    public static function prepareQueryParams(array $sideload = null, array $iterators = null)
    {
        $addParams = [];
        // First look for side-loaded variables
        if (is_array($sideload)) {
            $addParams['include'] = implode(',', $sideload);
        }

        // Next look for special collection iterators
        if (is_array($iterators)) {
            foreach ($iterators as $k => $v) {
                if (in_array($k, ['per_page', 'page', 'sort_order', 'sort_by'])) {
                    $addParams[$k] = $v;
                }
            }
        }

        return $addParams;
    }

    /**
     * Use the send method to call every endpoint except for oauth/tokens
     *
     * @param HttpClient $client
     * @param string $endPoint E.g. "/tickets.json"
     * @param array $options
     *                             Available options are listed below:
     *                             array $queryParams Array of unencoded key-value pairs, e.g. ["ids" => "1,2,3,4"]
     *                             array $postFields Array of unencoded key-value pairs, e.g. ["filename" => "blah.png"]
     *                             string $method "GET", "POST", etc. Default is GET.
     *                             string $contentType Default is "application/json"
     *
     * @return array The response body, parsed from JSON into an associative array
     * @throws ApiResponseException
     * @throws AuthException
     */
    public static function sendWithOptions(
        HttpClient $client,
        $endPoint,
        $options = []
    ) {
        $options = array_merge(
            [
                'method'      => 'GET',
                'contentType' => 'application/json',
                'postFields'  => null,
                'queryParams' => null
            ],
            $options
        );

        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => $options['contentType'],
        ];

        $request = new Request(
            $options['method'],
            $client->getApiUrl() . $endPoint,
            $headers
        );

        if (! empty($options['postFields'])) {
            $request = $request->withBody(\GuzzleHttp\Psr7\stream_for(json_encode($options['postFields'])));
        }

        if (! empty($options['queryParams'])) {
            foreach ($options['queryParams'] as $queryKey => $queryValue) {
                $uri     = $request->getUri();
                $uri     = $uri->withQueryValue($uri, $queryKey, $queryValue);
                $request = $request->withUri($uri, true);
            }
        }

        try {
            if ($client->getAuthStrategy() === HttpClient::AUTH_BASIC) {
                $authOptions = $client->getAuthOptions();
                $options     = ['auth' => [$authOptions['username'] . '/token', $authOptions['token'], 'basic']];
                $response    = $client->guzzle->send($request, $options);
            } elseif ($client->getAuthStrategy() === HttpClient::AUTH_OAUTH) {
                $authOptions = $client->getAuthOptions();
                $oAuthToken  = $authOptions['token'];
                $request     = $request->withAddedHeader('Authorization', ' Bearer ' . $oAuthToken);
                $response    = $client->guzzle->send($request);
            } else {
                throw new AuthException('Please set authentication to send requests.');
            }

            $client->setDebug(
                $response->getHeaders(),
                $response->getStatusCode(),
                10,
                null
            );
        } catch (RequestException $e) {
            throw new ApiResponseException($e);
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Specific case for OAuth. Run /oauth.php via your browser to get an access token
     *
     * @param HttpClient $client
     * @param string $code
     * @param string $oAuthId
     * @param string $oAuthSecret
     *
     * @throws \Exception
     * @return mixed
     */
    public static function oauth(HttpClient $client, $code, $oAuthId, $oAuthSecret)
    {
        $url = 'https://' . $client->getSubdomain() . '.zendesk.com/oauth/tokens';

        $protocol = ($_SERVER['HTTPS']) ? 'https://' : 'http://';

        $curl = (isset(self::$curl)) ? self::$curl : new CurlRequest;
        $curl->setopt(CURLOPT_URL, $url);
        $curl->setopt(CURLOPT_POST, true);
        $curl->setopt(CURLOPT_POSTFIELDS, json_encode([
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $oAuthId,
            'client_secret' => $oAuthSecret,
            'redirect_uri'  => $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
            'scope'         => 'read'
        ]));
        $curl->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $curl->setopt(CURLINFO_HEADER_OUT, true);
        $curl->setopt(CURLOPT_RETURNTRANSFER, true);
        $curl->setopt(CURLOPT_CONNECTTIMEOUT, 30);
        $curl->setopt(CURLOPT_TIMEOUT, 30);
        $curl->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setopt(CURLOPT_HEADER, true);
        $curl->setopt(CURLOPT_VERBOSE, true);
        $curl->setopt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setopt(CURLOPT_MAXREDIRS, 3);
        $response = $curl->exec();
        if ($response === false) {
            throw new \Exception(sprintf('Curl error message: "%s" in %s', $curl->error(), __METHOD__));
        }
        $headerSize     = $curl->getinfo(CURLINFO_HEADER_SIZE);
        $responseBody   = substr($response, $headerSize);
        $responseObject = json_decode($responseBody);
        $client->setDebug(
            $curl->getinfo(CURLINFO_HEADER_OUT),
            $curl->getinfo(CURLINFO_HTTP_CODE),
            substr($response, 0, $headerSize),
            (isset($responseObject->error) ? $responseObject : null)
        );
        $curl->close();
        self::$curl = null;

        return $responseObject;
    }
}
