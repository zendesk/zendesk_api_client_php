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

Inside of `composer.json` specify the following:

``` json
{
  "require": {
    "zendesk/zendesk_api_client_php": "dev-master"
  }
}
```

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
$tickets = $client->users($requesterId)->requests()->findAll();
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

### Side-loading

Side-loading allows you to retrieve related records as part of a single request. See [the documentation] for more information. (https://developer.zendesk.com/rest_api/docs/core/side_loading).

An example of sideloading with the client is shown below.

``` php
$tickets = $client->tickets()->sideload(['users', 'groups'])->findAll();
```

### Pagination
The Zendesk API offers a way to get the next pages for the requests and is documented in [the Zendesk Deveoloper Documentation](https://developer.zendesk.com/rest_api/docs/core/introduction#pagination).

The way to do this is to pass it as an option to your request.

``` php
$tickets = $this->client->tickets()->findAll(['per_page' => 10, 'page' => 2]);
```

The allowed options are
* per_page
* page
* sort_order


## Copyright and license

Copyright 2013-present Zendesk

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
