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


