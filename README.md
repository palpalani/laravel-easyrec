Laravel EasyRec
===============

[![Latest Version](https://img.shields.io/github/release/palpalani/laravel-easyrec.svg?style=flat)](https://github.com/palpalani/laravel-easyrec/releases)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/palpalani/laravel-easyrec.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-easyrec)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-easyrec/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/palpalani/laravel-easyrec/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-easyrec/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/palpalani/laravel-easyrec/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/palpalani/laravel-easyrec.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-easyrec)

## What is EasyRec?

EasyRec is an open source recommendation engine system that provides personalized recommendations using a RESTful API.

## The recommendation engine server

You can use the server and call the associated RESTful API maintained by the easyrec team or download easyrec and call
the API on one of your servers.

For additional information, take a look at the [easyrec website](http://easyrec.org).

#### Use EasyRec with the server maintained by the team

This is the ready-to-go solution. You may want to use this if you don't want to configure another server dedicated to
easyrec.

- Create an easyrec account: http://easyrec.org/register
- Open up your mailbox and activate your account
- Create a new Tenant in your dashboard
- Fill your API key, and your Tenant ID in the configuration file

#### Configure your own easyrec server

Take a look at the [easyrec installation guide](http://easyrec.sourceforge.net/wiki/index.php?title=Installation_Guide).

## Installation

[PHP](https://php.net) 8.0+ and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Easyrec, simply add the following line to the require block of your `composer.json`
file:

```
"palpalani/laravel-easyrec": "~2.0"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Fol old version of Laravel you need to register the extension with the blow information.
Once Laravel EasyRec is installed, you need to register the service provider. Open up `config/app.php` and add the
following to the `providers` key.

* `Antoineaugusti\LaravelEasyrec\LaravelEasyrecServiceProvider::class`

You can register the Easyrec facade in the `aliases` key of your `config/app.php` file if you like.

* `'Easyrec' => Antoineaugusti\LaravelEasyrec\Facades\LaravelEasyrec::class

## Configuration

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This will create a `config/easyrec.php` file in your app that you can modify to set your configuration. Also, make sure
you check for changes to the original config file in this package between releases.

## Usage

## Actions

The following variables are common to the actions methods.

##### Required parameters

- `$itemid`: An item ID to identify an item on your website. Eg: "POST42"
- `$itemdescription`: An item description that is displayed when showing recommendations on your website.
- `$itemurl`: An item URL that links to the item page. Please give an absolute path.
- `$sessionid`: A session ID of a user. If not given, will try to guess with the Session facade `Session::getId()`

##### Optional parameters

- `$userid`: A user ID.
- `$itemimageurl`: An optional item image URL that links to an imagine of the item. Please give an absolute path.
- `$actiontime`: An action time parameter that overwrites the current timestamp of the action. The parameter has the
  format "dd_MM_yyyy_HH_mm_ss".
- `$itemtype`: An item type that denotes the type of the item (`IMAGE`, `BOOK` etc.). If not supplied, the default
  value `ITEM` will be used. **Warning**: before specifying a custom `itemtype` you must add this custom item type by
  using the web administration panel of Easyrec.

#### Errors

If an error occurs, an exception `Antoineaugusti\LaravelEasyrec\Exceptions\EasyrecException` will be thrown with one of
the following code and message:

- code 299: `Wrong APIKey/Tenant combination!`
- code 301: `Item requires an id!`
- code 303: `Item requires a description!`
- code 304: `Item requires a URL!`
- code 305: `Item requires a valid Rating Value!` (only when calling the `rate` method)
- code 401: `A session id is required!`
- code 912: `Operation failed! itemType XXX not found for tenant YYY`

### View

This action should be raised if a user views an item.

##### Function signature

`Easyrec::view($itemid, $itemdescription, $itemurl, $userid = null, $itemimageurl = null, $actiontime = null, $itemtype = null, $sessionid = null)`

##### Parameters

Non-null variables in the function signature are required.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"action": "view",
	"tenantid": "EASYREC_DEMO",
	"userid": "24EH1723322222A3",
	"sessionid": "F3D4E3BE31EE3FA069F5434DB7EC2E34",
	"item": [
	  "id": "42",
	  "itemType": "ITEM",
	  "description": "Fatboy Slim - The Rockafeller Skank",
	  "url": "/item/fatboyslim"
	]
]
```

### Buy

This action should be raised if a user buys an item.

##### Function signature

`Easyrec::buy($itemid, $itemdescription, $itemurl, $userid = null, $itemimageurl = null, $actiontime = null, $itemtype = null, $sessionid = null)`

##### Parameters

Non-null variables in the function signature are required.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"tenantid": "EASYREC_DEMO",
	"action": "buy",
	"userid": "24EH1723322222A3",
	"sessionid": "F3D4E3BE31EE3FA069F5434DB7EC2E34",
	"item": [
	  "id": "42",
	  "type": "ITEM",
	  "description": "Fatboy Slim - The Rockafeller Skank",
	  "url": "/item/fatboyslim"
	]
]
```

### Rate

This action should be raised if a user rates an item.

##### Function signature

`Easyrec::rate($itemid, $ratingvalue, $itemdescription, $itemurl, $userid = null, $itemimageurl = null, $actiontime = null, $itemtype = null, $sessionid = null)`

##### Parameters

Non-null variables in the function signature are required. The rating value is an additional parameter.

- `$ratingvalue`: the rating value of the item. Must be an integer in the range from 1 to 10.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"tenantid": "rate",
	"action": "rate",
	"userid": "24EH1723322222A3",
	"sessionid": "F3D4E3BE31EE3FA069F5434DB7EC2E34",
	"item": [
	  "id": "42",
	  "type": "ITEM",
	  "description": "Fatboy Slim - The Rockafeller Skank",
	  "ratingValue": "10",
	  "url": "/item/fatboyslim"
	]
]
```

### Send a custom action

This action can be used to send generic user actions.

##### Function signature

`sendAction($itemid, $itemdescription, $itemurl, $actiontype, $actionvalue = null, $userid = null, $itemimageurl = null, $actiontime = null, $itemtype = null, $sessionid = null)`

##### Parameters

Non-null variables in the function signature are required. There are two addition parameters.

- `$actiontype`: A required action type you want to use to send. **You must create the action type in the web interface
  before you can use it in API calls.**
- `$actionvalue`: If your action type uses action values this parameter is required. It is used to save the action value
  of your action.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"tenantid": "rate",
	"action": "delete",
	"userid": "24EH1723322222A3",
	"sessionid": "F3D4E3BE31EE3FA069F5434DB7EC2E34",
	"item": [
	  "id": "42",
	  "type": "ITEM",
	  "description": "Fatboy Slim - The Rockafeller Skank",
	  "ratingValue": "10",
	  "url": "/item/fatboyslim"
	]
]
```

## Recommendations

The following variables are common to the recommendations methods.

##### Required parameters

- `$itemid`: An item ID to identify an item on your website. Eg: "POST42"

##### Optional parameters

- `$userid`: A user ID. If this parameter is provided, items viewed by this user are suppressed.
- `$numberOfResults`: determine the number of results returned. Must be an integer in the range from 1 to 15.
- `$itemtype`: An item type that denotes the type of the item (`IMAGE`, `BOOK` etc.). If not supplied, the default
  value `ITEM` will be used. **Warning**: before specifying a custom `itemtype` you must add this custom item type by
  using the web administration panel of Easyrec.
- `$requesteditemtype`: A type of an item (e.g. `IMAGE`, `VIDEO`, `BOOK`, etc.) to filter the returned items. If not
  supplied the default value `ITEM` will be used.
- `$withProfile`: If this parameter is set to `true` the result contains an additional element `profileData` with the
  item profile. Default value to `false`.

#### Errors

If an error occurs, an exception `Antoineaugusti\LaravelEasyrec\Exceptions\EasyrecException` will be thrown with one of
the following code and message:

- code 299: `Wrong APIKey/Tenant combination!`
- code 300: `Item does not exist!`
- code 403: `No Userd Id given!` (only when calling the `recommendationsForUser` method)
- code 912: `Operation failed! itemType XXX not found for tenant YYY`

### Users also viewed

Users who viewed the specified item also viewed the returned items.

##### Function signature

`Easyrec::usersAlsoViewed($itemid, $userid = null, $numberOfResults = 10, $itemtype = null, $requesteditemtype = null, $withProfile = false)`

##### Parameters

Non-null variables in the function signature are required.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"tenantid": "EASYREC_DEMO",
	"action": "otherUsersAlsoViewed",
	"user": [
		"id": "24EH1723322222A3"
	],
	"baseitem": [
	  "item": [
		"id": "42",
		"type": "ITEM",
		"description": "Fatboy Slim - The Rockafeller Skank",
		"url": "/item/fatboyslim"
	  ]
	],
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile":[
				"year": "1990"
			]
		],
		"url": "/item/beastieboys"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### Users also bought

Users who bought the specified item also bought the returned items.

##### Function signature

`Easyrec::usersAlsoBought($itemid, $userid = null, $numberOfResults = 10, $itemtype = null, $requesteditemtype = null, $withProfile = false)`

##### Parameters

Non-null variables in the function signature are required.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"tenantid": "EASYREC_DEMO",
	"action": "otherUsersAlsoBought",
	"user": [
		"id": "24EH1723322222A3"
	],
	"baseitem": [
	  "item": [
		"id": "42",
		"type": "ITEM",
		"description": "Fatboy Slim - The Rockafeller Skank",
		"url": "/item/fatboyslim"
	  ]
	],
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile":[
				"year": "1990"
			]
		],
		"url": "/item/beastieboys"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### Items rated good by other users

Users who rated the specified item "good" did the same with items returned by this method.

##### Function signature

`Easyrec::ratedGoodByOther($itemid, $userid = null, $numberOfResults = 10, $itemtype = null, $requesteditemtype = null, $withProfile = false)`

##### Parameters

Non-null variables in the function signature are required.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"tenantid": "EASYREC_DEMO",
	"action": "itemsRatedGoodByOtherUsers",
	"user": [
		"id": "24EH1723322222A3"
	],
	"baseitem": [
	  "item": [
		"id": "42",
		"type": "ITEM",
		"description": "Fatboy Slim - The Rockafeller Skank",
		"url": "/item/fatboyslim"
	  ]
	],
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile":[
				"year": "1990"
			]
		],
		"url": "/item/beastieboys"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### Recommendations for user

Returns recommendations for a given user ID.

##### Function signature

`Easyrec::recommendationsForUser($userid, $numberOfResults = 10, $requesteditemtype = null, $actiontype = "VIEW", $withProfile = false)`

##### Parameters

Non-null variables in the function signature are required. There is an additional parameter

- `$actiontype`: Allows to define which actions of a user are considered when creating the personalized recommendation.
  Valid values are: `VIEW`, `RATE`, `BUY`.

##### Example response

The response will be returned as a PHP array.

```shell
[
	"tenantid": "EASYREC_DEMO",
	"action": "recommendationsForUser",
	"user": [
		"id": "24EH1723322222A3"
	],
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile":[
				"year": "1990"
			]
		],
		"url": "/item/beastieboys"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### History for user

Returns items which were involved in the latest user actions.

##### Function signature

`Easyrec::actionHistoryForUser($userid, $numberOfResults = 10, $requesteditemtype = null, $actiontype = null)`

##### Parameters

Non-null variables in the function signature are required. There is an additional parameter

- `$actiontype`: Allows to define which actions of a user are considered when creating the personalized recommendation.
  Valid values are: `VIEW`, `RATE`, `BUY`.

##### Example response

The response will be returned as a PHP array.

```shell
[
	'action' => 'actionhistory',
	'recommendeditems' => [
		'item' => [
			0 => [
				'creationDate' => '2014-08-24 12:40:32.0',
				'description' => 'Quote 17982',
				'imageUrl' => [
					'@nil' => 'true'
				],
				'id' => '17982',
				'itemType' => 'QUOTE',
				'profileData' => [
					'@nil' => 'true'
				],
				'url' => 'http://example.com/quotes/17982'
			],
			1 => [
				'creationDate' => '2014-08-24 12:00:59.0',
				'description' => 'Quote 17987',
				'imageUrl' => [
					'@nil' => 'true'
				],
				'id' => '17987',
				'itemType' => 'QUOTE',
				'profileData' => [
					'@nil' => 'true'
				],
				'url' => 'http://example.com/quotes/17982'
			]
		]
	],
	'tenantid' => 'demo',
	'userid' => '27',
	'listids' => [
		0 => 17982,
		1 => 17987,
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

## Rankings

The following variables are common to the rankings methods.

##### Optional parameters

- `$numberOfResults`: determine the number of results returned. Must be an integer in the range from 1 to 50.
- `$requesteditemtype`: An item type that denotes the type of the item (`IMAGE`, `BOOK` etc.). If not supplied, the
  default value `ITEM` will be used.
- `$timeRange`: An optional parameter to determine the time range. Available values:
    - `DAY`: most viewed items within the last 24 hours
    - `WEEK`: most viewed items within the last week
    - `MONTH`: most viewed items within the last month
    - `ALL` (default): if no value or this value is given, the most viewed items of all time will be shown.
- `$withProfile`: If this parameter is set to `true` the result contains an additional element `profileData` with the
  item profile. Default value to `false`.

#### Errors

If an error occurs, an exception `Antoineaugusti\LaravelEasyrec\Exceptions\EasyrecException` will be thrown with one of
the following code and message:

- code 299: `Wrong APIKey/Tenant combination!`
- code 300: `Item does not exist!`
- code 912: `Operation failed! itemType XXX not found for tenant YYY`

### Most viewed items

Shows items that were viewed most by all users

##### Function signature

`Easyrec::mostViewedItems($numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)`

##### Example response

The response will be returned as a PHP array.

```
[
	"tenantid": "EASYREC_DEMO",
	"action": "mostViewedItems",
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile": [
				"year": "1990"
			]
		],
		"url": "/item/beastieboys",
		"imageurl": "/img/covers/beastieboys.jpg",
		"value": "111.0"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(\DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### Most bought items

Shows items that were bought the most.

##### Function signature

`Easyrec::mostBoughtItems($numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)`

##### Example response

The response will be returned as a PHP array.

```
[
	"tenantid": "EASYREC_DEMO",
	"action": "mostBoughtItems",
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile": [
				"year": "1990"
			]
		],
		"url": "/item/beastieboys",
		"imageurl": "/img/covers/beastieboys.jpg",
		"value": "111.0"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(\DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### Most rated items

Shows items that were rated the most.

##### Function signature

`Easyrec::mostRatedItems($numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)`

##### Example response

The response will be returned as a PHP array.

```
[
	"tenantid": "EASYREC_DEMO",
	"action": "mostRatedItems",
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile": [
				"year": "1990"
			]
		],
		"url": "/item/beastieboys",
		"imageurl": "/img/covers/beastieboys.jpg",
		"value": "111.0"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(\DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### Best rated items

Shows the best rated items. The ranking only includes items that have **an average ranking value greater than 5.5.**

##### Function signature

`Easyrec::bestRatedItems($numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)`

##### Example response

The response will be returned as a PHP array.

```
[
	"tenantid": "EASYREC_DEMO",
	"action": "bestRatedItems",
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile": [
				"year": "1990"
			]
		],
		"url": "/item/beastieboys",
		"imageurl": "/img/covers/beastieboys.jpg",
		"value": "111.0"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(\DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

### Worst rated items

Shows the worst rated items. The ranking only includes items that have **an average ranking value less than 5.5.**

##### Function signature

`Easyrec::worstRatedItems($numberOfResults = 30, $timeRange = 'ALL', $requesteditemtype = null, $withProfile = false)`

##### Example response

The response will be returned as a PHP array.

```
[
	"tenantid": "EASYREC_DEMO",
	"action": "worstRatedItems",
	"recommendeditems": [
	  "item": [
		"id": "43",
		"type": "ITEM",
		"description": "Beastie Boys - Intergalactic",
		"profileData": [
			"profile": [
				"year": "1990"
			]
		],
		"url": "/item/beastieboys",
		"imageurl": "/img/covers/beastieboys.jpg",
		"value": "111.0"
	  ]
	],
	"listids": [
		43
	]
]
```

##### Retrieving your models

Note that your models can be retrieved using this simple code:

```php
YourModel::whereIn('id', $result['listids'])->get();
```

If you want to keep the order of the items, you can use this code if you are using **MySQL**:

```php
$ids = $result['listids']:
YourModel::whereIn('id', $ids)
	->orderBy(\DB::raw('FIELD(`id`, '.implode(',', $ids).')'))
	->get();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/palpalani/laravel-easyrec/tags).

## Credits

- [palPalani](https://github.com/palPalani)
- [All Contributors](../../contributors)

## License

The Apache License. Please see [License File](LICENSE.md) for more information.
