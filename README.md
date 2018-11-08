# Zendesk PHP API Client Library #

[![Build Status](https://travis-ci.org/zendesk/zendesk_api_client_php.svg?branch=master)](https://travis-ci.org/zendesk/zendesk_api_client_php)
[![Latest Stable Version](https://poser.pugx.org/zendesk/zendesk_api_client_php/v/stable)](https://packagist.org/packages/zendesk/zendesk_api_client_php)
[![Total Downloads](https://poser.pugx.org/zendesk/zendesk_api_client_php/downloads)](https://packagist.org/packages/zendesk/zendesk_api_client_php)
[![Code Climate](https://codeclimate.com/github/zendesk/zendesk_api_client_php/badges/gpa.svg)](https://codeclimate.com/github/zendesk/zendesk_api_client_php)
[![License](https://poser.pugx.org/zendesk/zendesk_api_client_php/license)](https://packagist.org/packages/zendesk/zendesk_api_client_php)

## API Client Version

This is the second version of our PHP API client. The previous version of the API client can be found on the [v1 branch](https://github.com/zendesk/zendesk_api_client_php/tree/v1).

## API version support

This client **only** supports Zendesk's API v2.  Please see our [API documentation](http://developer.zendesk.com) for more information.

## Requirements
* PHP 5.5+

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
The Zendesk API offers a way to get the next pages for the requests and is documented in [the Zendesk Developer Documentation](https://developer.zendesk.com/rest_api/docs/core/introduction#pagination).

The way to do this is to pass it as an option to your request.

``` php
$tickets = $this->client->tickets()->findAll(['per_page' => 10, 'page' => 2]);
```

The allowed options are
* per_page
* page
* sort_order

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

To help would be contributors, we've added a REPL tool. It is a simple wrapper for [psysh](http://psysh.org) and symfony's console.
On your terminal, run `bin/console <subdomain> <email> <api token>`. This would automatically create an instance of `Zendesk\API\HttpClient` on $client variable.
After that you would be able to enter any valid php statement. The goal of the tool is to speed up the process in which developers
can experiment on the code base.

## Copyright and license

Copyright 2013-present Zendesk

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
