# Zendesk PHP API Client Library #

## API version support

This client **only** supports Zendesk's v2 API.  Please see our [API documentation](http://developer.zendesk.com/api-docs) for more information.

## Installation

The Zendesk PHP API client can be installed using [Composer](https://packagist.org/packages/zendesk/zendesk_api_client_php).

### Composer

Inside of composer.json specify the following:

```json
{
  "require": {
    "zendesk/zendesk_api_client_php": "dev-master"
  }
}
```

## Configuration

Configuration is done through an instance of Zendesk\API\Client.
The block is mandatory and if not passed, an error will be thrown.

```php
use Zendesk\API\Client as ZendeskAPI;

$subdomain = "subdomain";
$username  = "username";
$token     = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv"; // replace this with your token
// $password = "123456";

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password
```

## Usage

### Basic Operations

```php
// Get all tickets
$tickets = $client->tickets()->findAll();
print_r($tickets);

// Create a new ticket
$newTicket = $client->tickets()->create(array(
  'subject' => 'The quick brown fox jumps over the lazy dog',
  'comment' => array (
      'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
  ),
  'priority' => 'normal'
));
print_r($newTicket);

// Update multiple tickets
$client->ticket(array (123, 456))->update(array (
  'status' => 'urgent'
));

// Delete a ticket
$client->ticket(123)->delete();
```

### Attachments

```php
$attachment = $client->attachments()->upload(array(
    'file' => getcwd().'/tests/assets/UK.png',
    'type' => 'image/png',
    'name' => 'UK.png'    // Optional parameter, will default to filename.ext
));
```

### Side-loading

```php
$tickets = $this->client->tickets()->sideload(array('users', 'groups'))->findAll();
```

## Note on Patches/Pull Requests
1. Fork the project.
2. Make your feature addition or bug fix.
3. Add tests for it. This is important so I don't break it in a future version
   unintentionally.
4. Commit and do not mess with version or history. (if you want to have
   your own version, that is fine but bump version in a commit by itself I can
   ignore when I pull)
5. Send a pull request. Bonus points for topic branches.

## Copyright and license

Copyright 2013 Zendesk

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
