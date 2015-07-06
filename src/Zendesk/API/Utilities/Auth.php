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

    /**
     * @var string
     */
    protected $authStrategy;
    /**
     * @var Array
     */
    protected $authOptions;

    /**
     * Auth constructor.
     *
     * @param       $strategy
     * @param array $options
     *
     * @throws AuthException
     *
     */
    public function __construct($strategy, array $options)
    {
        $validAuthStrategies = [Auth::BASIC, Auth::OAUTH];
        if (! in_array($strategy, $validAuthStrategies)) {
            throw new AuthException('Invalid auth strategy set, please use `'
                                    . implode('` or `', $validAuthStrategies)
                                    . '`');
        }

        $this->authStrategy = $strategy;

        if ($strategy == Auth::BASIC) {
            if (! array_key_exists('username', $options) || ! array_key_exists('token', $options)) {
                throw new AuthException('Please supply `username` and `token` for basic auth.');
            }
        } elseif ($strategy == Auth::OAUTH) {
            if (! array_key_exists('token', $options)) {
                throw new AuthException('Please supply `token` for oauth.');
            }
        }

        $this->authOptions = $options;
    }

    /**
     * @param RequestInterface $request
     * @param array            $requestOptions
     *
     * @return array
     * @throws AuthException
     */
    public function prepareRequest(RequestInterface $request, array $requestOptions = [])
    {
        if ($this->authStrategy === self::BASIC) {
            $requestOptions = array_merge($requestOptions, [
                'auth' => [
                    $this->authOptions['username'] . '/token',
                    $this->authOptions['token'],
                    'basic'
                ]
            ]);
        } elseif ($this->authStrategy === Auth::OAUTH) {
            $oAuthToken = $this->authOptions['token'];
            $request    = $request->withAddedHeader('Authorization', ' Bearer ' . $oAuthToken);
        } else {
            throw new AuthException('Please set authentication to send requests.');
        }

        return [$request, $requestOptions];
    }
}
