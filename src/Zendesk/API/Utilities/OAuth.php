<?php

namespace Zendesk\API\Utilities;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Zendesk\API\Exceptions\ApiResponseException;

class OAuth
{
    /**
     * Requests for an access token.
     *
     * @param Client $client
     * @param string $subdomain
     * @param array  $params
     *
     * @param string $domain
     * @return array
     * @throws ApiResponseException
     */
    public static function getAccessToken(Client $client, $subdomain, array $params, $domain = 'zendesk.com')
    {
        $authUrl  = "https://$subdomain.$domain/oauth/tokens";

        // Fetch access_token
        $params = array_merge([
            'code'          => null,
            'client_id'     => null,
            'client_secret' => null,
            'grant_type'    => 'authorization_code',
            'scope'         => 'read write',
            'redirect_uri'  => null,
        ], $params);

        try {
            $request = new Request('POST', $authUrl, ['Content-Type' => 'application/json']);
            $request = $request->withBody(\GuzzleHttp\Psr7\stream_for(json_encode($params)));
            $response = $client->send($request);
        } catch (RequestException $e) {
            throw new ApiResponseException($e);
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Generates an oAuth URL.
     *
     * @param string $subdomain
     * @param array  $options
     * @param string $domain
     * @return string
     */
    public static function getAuthUrl($subdomain, array $options, $domain = 'zendesk.com')
    {
        $queryParams = [
            'response_type' => 'code',
            'client_id'    => null,
            'state'        => null,
            'redirect_uri' => null,
            'scope'        => 'read write',
        ];

        $options = array_merge($queryParams, $options);

        $oAuthUrl = "https://$subdomain.$domain/oauth/authorizations/new?";
        // Build query and remove empty values
        $oAuthUrl .= http_build_query(array_filter($options));

        return $oAuthUrl;
    }
}
