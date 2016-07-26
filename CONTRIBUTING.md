## Note on Patches/Pull Requests
1. Fork the project.
2. Make your feature addition or bug fix.
3. Add tests for it. This is important so that we don't break your improvement in a future version unintentionally.
4. Please follow the [coding standard described above](#coding-standard).
4. Commit and do not mess with version or history. (If you want to have
   your own version, that is fine but bump version in a commit by itself I can
   ignore when we pull)
5. Send a pull request. Bonus points for topic branches.

### Test suite

The test suite is run via phpunit. Note that these are all live tests that must be run targeted at a real Zendesk instance. Credentials can be provided by setting the environment variables in phpunit.xml; a sample is provided at phpunit.xml.dist.

To run the unit tests: `vendor/bin/phpunit --testsuite "Zendesk API Unit Test Suites"`

To run the live tests: `vendor/bin/phpunit --testsuite "Zendesk API Live Test Suites"`


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

