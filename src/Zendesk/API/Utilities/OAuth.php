<?php

namespace Zendesk\API\Utilities;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Zendesk\API\Exceptions\ApiResponseException;

class OAuth
{
    /**
     * Requests for an access token.
     *
     * @param Client $client
     * @param string $subDomain
     * @param array  $params
     *
     * @return array
     * @throws ApiResponseException
     */
    public static function getAccessToken(Client $client, $subDomain, array $params)
    {
        $authUrl  = 'https://' . $subDomain . '.zendesk.com/oauth/tokens';
        $protocol = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';

        // Fetch access_token
        $params = array_merge([
            'code'          => null,
            'client_id'     => null,
            'client_secret' => null,
            'grant_type'    => 'authorization_code',
            'scope'         => 'read write',
            'redirect_uri'  => $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
        ], $params);

        try {
            $response = $client->post($authUrl, ['form_params' => $params]);
        } catch (RequestException $e) {
            throw new ApiResponseException($e);
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Generates an oAuth URL.
     *
     * @param       $subDomain
     * @param array $options
     *
     * @return string
     */
    public static function getAuthUrl($subDomain, array $options)
    {
        $queryParams = [
            'response_type' => 'code',
            'client_id'    => null,
            'state'        => null,
            'redirect_uri' => null,
            'scope'        => 'read write',
        ];

        $options = array_merge($queryParams, $options);

        $oAuthUrl = "https://$subDomain.zendesk.com/oauth/authorizations/new?";
        // Build query and remove empty values
        $oAuthUrl .= http_build_query(array_filter($options));

        return $oAuthUrl;
    }
}
