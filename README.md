# Zendesk PHP API Client Library #

[![Build Status](https://travis-ci.org/zendesk/zendesk_api_client_php.svg?branch=master)](https://travis-ci.org/zendesk/zendesk_api_client_php)
[![Code Climate](https://codeclimate.com/github/zendesk/zendesk_api_client_php/badges/gpa.svg)](https://codeclimate.com/github/zendesk/zendesk_api_client_php)

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
$username  = "email@company.com";
$token     = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv"; // replace this with your token

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);
```

## Usage

### Basic Operations

``` php
// Get all tickets
$tickets = $client->tickets()->findAll();
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
```

### Attachments

``` php
$attachment = $client->attachments()->upload([
    'file' => getcwd().'/tests/assets/UK.png',
    'type' => 'image/png',
    'name' => 'UK.png' // Optional parameter, will default to filename.ext
]);
```

### Test suite

The test suite is run via phpunit. Note that these are all live tests that must be run targeted at a real Zendesk instance. Credentials can be provided by setting the environment variables in phpunit.xml; a sample is provided at phpunit.xml.dist.

To run the unit tests: `vendor/bin/phpunit --testsuite "Zendesk API Unit Test Suites"`

To run the live tests: `vendor/bin/phpunit --testsuite "Zendesk API Live Test Suites"`


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


## Coding Standard

This project strictly follows the [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standard.

[PHP Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer) is used to verify that the standard is being followed.

In addition to the PSR2 standard which we try to follow the following rules as much as possible:

### PHPDoc

All Classes, Class Methods and Properties should have docblocs.

#### Classes

Class docblocks should contain:
* A short description of the class
* Any methods available that are called via magic method with what that method returns.

A good example is
``` php
/**
 * Client class, base level access
 *
 * @method Debug debug()
 * @method Tickets ticket()
 * @method Views views()
 */
```


#### Methods

Method docblocks should contain:
* A short description of what the method does.
* The parameters passed with what type to expect.
* Description of the parameters passed with examples(optional).
* The type of the return.
* All the possible exceptions the method may throw.

A good example of this is

``` php
/**
 * Find a specific ticket by id or series of ids
 *
 * @param integer|null $id
 * @param array        $queryParams
 *
 * @return Array
 *
 * @throws MissingParametersException
 * @throws \Exception
 */
```

#### Properties

Class properties docblocs should contain:
* A short description of the property (optional)
* The var type

A good example of this

``` php
/**
 * This contains the Auth object to be used for authenticating with the Client
 *
 * @var Zendesk\API\Utilities\Auth
 */
```

### Arrays
The short notations for declaring arrays (`[]`) is preferred over the longer `array()`.

Align `=>`s following the longest key to make the arrays easier to read.

``` php
[
    'findAll'             => "users/{userId}/{$this->resourceName}.json",
    'find'                => "users/{userId}/{$this->resourceName}/{id}.json",
    'update'              => "users/{userId}/{$this->resourceName}/{id}.json",
    'makePrimary'         => "users/{userId}/{$this->resourceName}/{id}/make_primary.json",
    'verify'              => "users/{userId}/{$this->resourceName}/{id}/verify.json",
    'requestVerification' => "users/{userId}/{$this->resourceName}/{id}/request_verification.json",
    'delete'              => "users/{userId}/{$this->resourceName}/{id}.json",
    'create'              => "users/{userId}/{$this->resourceName}.json",
    'createAsEndUser'     => "end_users/{userId}/{$this->resourceName}.json",
]
```

### Grouped assignment statements

Align the `=` for grouped assignment statements.

``` php
$headers             = 'sample';
$lastRequestBody     = 'example';
$lastResponseCode    = 'something';
$lastResponseHeaders = 'test';
$lastResponseError   = 'test2';

```

### Traits

#### Declaration

* Traits are added after class constants and arranged alphabetically when declared.
* Group traits accordingly by adding a new line after each group.
* Groups are ordered as follows:
1. Instantiator
2. Single resource
3. Bulk traits

#### Resource Traits

When adding a resource, use traits to define available API calls. Resource traits are namespaced under `Zendesk\API\Traits\Resource`.

**Single Resource**
* Create
* Delete
* Find
* FindAll
* Update
* Defaults - this adds **Find**, **FindAll**, **Create**, **Update**, and **Delete**

**Bulk traits**
* CreateMany
* DeleteMany
* FindMany
* UpdateMany
* CreateOrUpdateMany

#### Utility Traits

Use `Zendesk\API\Traits\Utility\InstantiatorTrait` when you want a resource to be chainable to other resources. See `Zendesk/API/Resources/Tickets.php`.

``` php
$this->client->tickets()->comments()->findAll();
```

## Note on Patches/Pull Requests
1. Fork the project.
2. Make your feature addition or bug fix.
3. Add tests for it. This is important so that we don't break your improvement in a future version unintentionally.
4. Please follow the [coding standard described above](#coding-standard).
4. Commit and do not mess with version or history. (If you want to have
   your own version, that is fine but bump version in a commit by itself I can
   ignore when we pull)
5. Send a pull request. Bonus points for topic branches.

## Copyright and license

Copyright 2013-present Zendesk

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
