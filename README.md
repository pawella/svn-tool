# Svn Tools by comopser

Read subversion repository and created packages.json file by composer usage

## Getting Started

Provides a service site that scans your external, private [SVN](https://subversion.apache.org/) repository, preparing a package.json file that will be used by [composer](https://getcomposer.org).

### Installing

Add a library to your project.

```
composer require pawella/svn-tool
```

### Sample application


```php
<?php

require __DIR__.'/../../vendor/autoload.php';

use SvnTool\Service;
use Zend\Cache\StorageFactory;


$service = new Service();

/**
 * Usage caching is optional.
 */
$cache = StorageFactory::factory([
    'adapter' => [
        'name'    => 'Filesystem',
        'options' => [
            'ttl' => 60,
            'cache_dir' => __DIR__.'/../cache'
        ],
    ],
    'plugins' => [
        'exception_handler' => ['throw_exceptions' => false],
        'Serializer'
    ],
]);
$service->setCache($cache);

$service->setRepositories([
    'https://userByRepo:password@svn.example.com/svn/repository',
    'https://userByRepo:password@svn.otherhost.com/repository'
]);

$service->responseJsonPackage();


```

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE) file for details

## Acknowledgments

* Hat tip to anyone who's code was used
* Inspiration
* etc
