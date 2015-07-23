## MultisiteDirectoryResolver

Adds filters that correct directory paths in a Wordpress multisite install with the WordPress installation in a custom subfolder. 

## Master

[![Quality Score](https://img.shields.io/scrutinizer/g/gwa/WpMultisiteDirectoryResolver.svg?style=flat-square)](https://scrutinizer-ci.com/g/gwa/WpMultisiteDirectoryResolver/code-structure/master)
[![Build Status](https://api.travis-ci.org/gwa/WpMultisiteDirectoryResolver.svg?branch=master&style=flat-square)](https://travis-ci.org/gwa/WpMultisiteDirectoryResolver)

## Develop

[![Quality Score](https://img.shields.io/scrutinizer/g/gwa/WpMultisiteDirectoryResolver.svg?style=flat-square)](https://scrutinizer-ci.com/g/gwa/WpMultisiteDirectoryResolver/code-structure/master)
[![Build Status](https://api.travis-ci.org/gwa/WpMultisiteDirectoryResolver.svg?branch=master&style=flat-square)](https://travis-ci.org/gwa/WpMultisiteDirectoryResolver)

## Usage

### Requirements

PHP 5.4.0 or above

### Installation

Install through composer.

```
composer require gwa/multisite-directory-resolver
```

### How to use

```php
// project root: path/to/project
// wp install:   path/to/project/custom/install/path

// choose which resolver you like to use
// 1. MultisiteResolverManager::TYPE_FOLDER - Use only for sub folder url handling -> example.com/site1/../..
// 2. MultisiteResolverManager::TYPE_SUBDOMAIN - Use only for sub domain handling -> test.example.com

$mdr = new Gwa\Wordpress\MultisiteDirectoryResolver('custom/install/path', $resolver);
$mdr->init();
```

Set you cookie like this to resolve the wordpress multisite redirect Loop.

```php
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
define('ADMIN_COOKIE_PATH', '/');
```

## Contributing

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a feature branch, and send us a pull request.

To ensure a consistent code base, you should make sure the code follows
the [Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html)
which we borrowed from Symfony.

The easiest way to do make sure you're following the coding standard is to run `vendor/bin/php-cs-fixer fix` before committing.

If you would like to help take a look at the [list of issues](https://github.com/gwa/WpMultisiteDirectoryResolver/issues).

## Authors

Great White Ark - <bannert@greatwhiteark.com> - <http://www.greatwhiteark.com><br />

### License

The Narrowspark framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
