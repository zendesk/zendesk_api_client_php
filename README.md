# THIS REPOSITORY IS NO LONGER SUPPORTED #

# Zendesk PHP API Client Library #

![CI](https://github.com/zendesk/zendesk_api_client_php/actions/workflows/ci.yaml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/zendesk/zendesk_api_client_php/v/stable)](https://packagist.org/packages/zendesk/zendesk_api_client_php)
[![Total Downloads](https://poser.pugx.org/zendesk/zendesk_api_client_php/downloads)](https://packagist.org/packages/zendesk/zendesk_api_client_php)
[![Code Climate](https://codeclimate.com/github/zendesk/zendesk_api_client_php/badges/gpa.svg)](https://codeclimate.com/github/zendesk/zendesk_api_client_php)
[![License](https://poser.pugx.org/zendesk/zendesk_api_client_php/license)](https://packagist.org/packages/zendesk/zendesk_api_client_php)

## API Client Version

This is the second version of our PHP API client. The previous version of the API client can be found on the [v1 branch](https://github.com/zendesk/zendesk_api_client_php/tree/v1).

## API version support

This client **only** supports Zendesk's API v2.  Please see our [API documentation](http://developer.zendesk.com) for more information.

## Requirements

* PHP 7.4+

## Installation

The Zendesk PHP API client can be installed using [Composer](https://packagist.org/packages/zendesk/zendesk_api_client_php).

### Composer

To install run `composer require zendesk/zendesk_api_client_php`

### Upgrading from V1 to V2

If you are upgrading from [v1](https://github.com/zendesk/zendesk_api_client_php/tree/v1) of the client, we've written an [upgrade guide](https://github.com/zendesk/zendesk_api_client_php/wiki/Upgrading-from-v1-to-v2) to highlight some of the key differences.

## Configuration

Configuration is done through an instance of `Zendesk\API\HttpClient`.
The block is mandatory and if not passed, an error will be thrown.

``` php
// load Composer
require 'vendor/autoload.php';

use Zendesk\API\HttpClient as ZendeskAPI;

$subdomain = "subdomain";
$username  = "email@example.com"; // replace this with your registered email
$token     = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv"; // replace this with your token

$client = new ZendeskAPI($subdomain);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);
```

## Usage

### Basic Operations

``` php
// Get all tickets
$tickets = $client->tickets()->findAll();
print_r($tickets);

// Get all tickets regarding a specific user.
$tickets = $client->users($requesterId)->tickets()->requested();
print_r($tickets);

// Create a new ticket
$newTicket = $client->tickets()->create([
    'subject'  => 'The quick brown fox jumps over the lazy dog',
    'comment'  => [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, ' .
                  'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
    ],
    'priority' => 'normal'
]);
print_r($newTicket);

// Update a ticket
$client->tickets()->update(123,[
    'priority' => 'high'
]);

// Delete a ticket
$client->tickets()->delete(123);

// Get all users
$users = $client->users()->findAll();
print_r($users);
```

### Attachments

``` php
$attachment = $client->attachments()->upload([
    'file' => getcwd().'/tests/assets/UK.png',
    'type' => 'image/png',
    'name' => 'UK.png' // Optional parameter, will default to filename.ext
]);
```

Attaching files to comments

``` php
$ticket = $client->tickets()->create([
    'subject' => 'The quick brown fox jumps over the lazy dog',
    'comment' => [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, ' .
                  'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'uploads'   => [$attachment->upload->token]
    ]
]);
```

### Side-loading

Side-loading allows you to retrieve related records as part of a single request. See [the documentation] for more information. (https://developer.zendesk.com/rest_api/docs/core/side_loading).

An example of sideloading with the client is shown below.

``` php
$tickets = $client->tickets()->sideload(['users', 'groups'])->findAll();
```

### Pagination

Methods like `findAll()` call the API without any pagination parameter. If an endpoint supports pagination, only the first page will be returned. To fetch all resources, you need to make multiple API calls.

#### Iterator (recommended)

The use of the correct type of pagination is encapsulated using an iterator, which allows you to retrieve all resources in all pages, making multiple API calls, without having to worry about pagination at all:

```php
$iterator = $client->tickets()->iterator();

foreach ($iterator as $ticket) {
    echo($ticket->id . " ");
}
```

If you want a specific sort order, please refer to the sorting section in the documentation ([Tickets, for example](https://developer.zendesk.com/api-reference/ticketing/tickets/tickets/#sorting)).

##### Iterator with params example

```php
$params = ['my' => 'param1', 'extra' => 'param2'];
$iterator = $client->tickets()->iterator($params);

foreach ($iterator as $ticket) {
    echo($ticket->id . " ");
}
```

* Change page size with: `$params = ['page[size]' => 5];`
* Change sorting with: `$params = ['sort' => '-updated_at'];`
  * Refer to the docs for details, including allowed sort fields
* Combine everything: `$params = ['page[size]' => 2, 'sort' => 'updated_at', 'extra' => 'param'];`

**Note**:

* Refer to the documentation for the correct params for sorting with the pagination type you're using
* The helper method `iterator_to_array` doesn't work with this implementation

##### Iterator API call response

The latest response is exposed in the iterator at `$iterator->latestResponse()`. This could come handy for debugging.

##### Custom iterators

If you want to use the iterator for custom methods, as opposed to the default `findAll()`, you can create an iterator for your collection:

```php
$strategy = new CbpStrategy( // Or ObpStrategy or SinglePageStrategy
    "resources_key", // The root key with resources in the response, usually plural and in underscore
    [], // Extra params for your call
);
$iterator = PaginationIterator($client->tickets(), $strategy);
foreach ($ticketsIterator as $ticket) {
    // Use as normal
}
```

This can be useful for filter endpoints like [active automations](https://developer.zendesk.com/api-reference/ticketing/business-rules/automations/#list-active-automations). However, in this common case where you only need to change the method from `findAll()` to `findActive()` there's a better shortcut:

```php
$iterator = $client->automations()->iterator($params, 'findActive');
```

Which is analogous to:

```php
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;
use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
$strategy = new CbpStrategy('automations', $params);
$iterator = new PaginationIterator(
    $client->automations(),
    $strategy,
    'findActive'
);
```

See how the [Pagination Trait](src/Zendesk/API/Traits/Resource/Pagination.php) is used if you need more custom implementations.

##### Catching API errors

This doesn't change too much:

```php
try {
    foreach ($iterator as $ticket) {
        // your code
    }
} catch (ApiResponseException $e) {
    $errorMessage = $e->getMessage();
    $errorDetails = $e=>getErrorDetails();
}
```

If you need to know at what point you got the error, you can store the required information inside the loop in your code.

#### FindAll using CBP (fine)

If you still want use `findAll()`, until CBP becomes the default API response, you must explicitly request CBP responses by using the param `page[size]`.

``` php
// CBP: /path?page[size]=100
$response = $client->tickets()->findAll(['page[size]' => 100]);
process($response->tickets); // Your implementation
do {
    if ($response->meta->has_more) {
        // CBP: /path?page[after]=cursor
        $response = $client->tickets()->findAll(['page[after]' => $response->meta->after_cursor]);
        process($response->tickets);
    }
} while ($response->meta->has_more);
```

**Process data _immediately_ upon fetching**. This optimizes memory usage, enables real-time processing, and helps adhere to API rate limits, enhancing efficiency and user experience.

#### Find All using OBP (only recommended if the endpoint doesn't support CBP)

If CBP is not available, this is how you can fetch one page at a time:

```php
$pageSize = 100;
$pageNumber = 1;
do {
    // OBP: /path?per_page=100&page=2
    $response = $client->tickets()->findAll(['per_page' => $pageSize, 'page' => $pageNumber]);
    process($response->tickets); // Your implementation
    $pageNumber++;
} while (count($response->tickets) == $pageSize);
```

**Process data _immediately_ upon fetching**. This optimizes memory usage, enables real-time processing, and helps adhere to API rate limits, enhancing efficiency and user experience.

### Retrying Requests

Add the `RetryHandler` middleware on the `HandlerStack` of your `GuzzleHttp\Client` instance. By default `Zendesk\Api\HttpClient`
retries:

* timeout requests
* those that throw `Psr\Http\Message\RequestInterface\ConnectException:class`
* and those that throw `Psr\Http\Message\RequestInterface\RequestException:class` that are identified as ssl issue.

#### Available options

Options are passed on `RetryHandler` as an array of values.

* max = 2 _limit of retries_
* interval = 300 _base delay between retries in milliseconds_
* max_interval = 20000 _maximum delay value_
* backoff_factor = 1 _backoff factor_
* exceptions = [ConnectException::class] _Exceptions to retry without checking retry_if_
* retry_if = null _callable function that can decide whether to retry the request or not_

## Contributing

Pull Requests are always welcome but before you send one please read our [contribution guidelines](#CONTRIBUTING.md). It would
speed up the process and would make sure that everybody follows the community's standard.

### Debugging

#### REPL

To help would be contributors, we've added a REPL tool. It is a simple wrapper for [psysh](http://psysh.org) and symfony's console.
On your terminal, run `bin/console <subdomain> <email> <api token>`. This would automatically create an instance of `Zendesk\API\HttpClient` on $client variable.
After that you would be able to enter any valid php statement. The goal of the tool is to speed up the process in which developers
can experiment on the code base.

#### HTTP client print API calls

You can print a line with details about every API call with:

```php
$client = new ZendeskAPI($subdomain);
$client->log_api_calls = true;
```

#### HTTP client debug

You can inspect this object for info about requests and responses:

```php
$client->getDebug();
```

## Copyright and license

Copyright 2013-present Zendesk

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
