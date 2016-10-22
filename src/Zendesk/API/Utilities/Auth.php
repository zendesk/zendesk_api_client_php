<?php

namespace Zendesk\API\Utilities;

use Psr\Http\Message\RequestInterface;
use Zendesk\API\Exceptions\AuthException;

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
     * @var array
     */
    protected $authOptions;

    /**
     * Returns an array containing the valid auth strategies
     *
     * @return array
     */
    protected static function getValidAuthStrategies()
    {
        return [self::BASIC, self::OAUTH];
    }

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
        if (! in_array($strategy, self::getValidAuthStrategies())) {
            throw new AuthException('Invalid auth strategy set, please use `'
                                    . implode('` or `', self::getValidAuthStrategies())
                                    . '`');
        }

        $this->authStrategy = $strategy;

        if ($strategy == self::BASIC) {
            if (! array_key_exists('username', $options) || ! array_key_exists('token', $options)) {
                throw new AuthException('Please supply `username` and `token` for basic auth.');
            }
        } elseif ($strategy == self::OAUTH) {
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
        } elseif ($this->authStrategy === self::OAUTH) {
            $oAuthToken = $this->authOptions['token'];
            $request    = $request->withAddedHeader('Authorization', ' Bearer ' . $oAuthToken);
        } else {
            throw new AuthException('Please set authentication to send requests.');
        }

        return [$request, $requestOptions];
    }
}
