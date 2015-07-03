<?php

namespace Zendesk\API\Utilities;

use Psr\Http\Message\RequestInterface;
use Zendesk\API\Exceptions\AuthException;
use Zendesk\API\HttpClient;

/**
 * Class Auth
 * This helper would manage all Authentication related operations.
 */
class Auth
{
    /**
     * The authentication setting to use an OAuth Token.
     */
    const OAUTH = 'oauth';
    /**
     * The authentication setting to use Basic authentication with a username and API Token.
     */
    const BASIC = 'basic';

    public static function prepareRequest(HttpClient $client, RequestInterface $request, array $requestOptions = [])
    {
        if ($client->getAuthStrategy() === self::BASIC) {
            $authOptions    = $client->getAuthOptions();
            $requestOptions = array_merge($requestOptions, [
                'auth' => [
                    $authOptions['username'] . '/token',
                    $authOptions['token'],
                    'basic'
                ]
            ]);
        } elseif ($client->getAuthStrategy() === Auth::OAUTH) {
            $authOptions = $client->getAuthOptions();
            $oAuthToken  = $authOptions['token'];
            $request     = $request->withAddedHeader('Authorization', ' Bearer ' . $oAuthToken);
        } else {
            throw new AuthException('Please set authentication to send requests.');
        }

        return [$request, $requestOptions];

    }
}
