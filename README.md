# Zendesk PHP API Client Library #

## API version support

This client **only** supports Zendesk's v2 API.  Please see our [API documentation](http://developer.zendesk.com/api-docs) for more information.

## Installation

The Zendesk PHP API client can be installed using [Composer](https://packagist.org/packages/zendesk/zendesk_api_client_php).

### Composer

Inside of composer.json specify the following:

````
{
    "require": {
        "zendesk/zendesk_api_client_php": "dev-master"
    }
}
````

## Configuration

Configuration is done through an instance of Zendesk\API\Client.
The block is mandatory and if not passed, an error will be thrown.

````
use Zendesk\API\Client as ZendeskAPI;

$subdomain = "subdomain";
$username = "username";
$token = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv"; // replace this with your token
//$password = "123456";

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password
````