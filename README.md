# Svn Tolls by comopser

Odczytuje repository subversion z projektami composer tworząc plik packages

## Getting Started

I will write about it one day

### Installing

I will write about it one day

```
Give the example
```

And repeat

```
until finished
```



### Przykładowe zastosowanie

Explain what these tests test and why

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
