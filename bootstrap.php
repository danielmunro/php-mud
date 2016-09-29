<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

$container = new \League\Container\Container();

$container->add(\PhpMud\Enum\ContainerService::EM, function() {

    $isDevMode = true;
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([__DIR__.'/src/Entity'], $isDevMode);

    return \Doctrine\ORM\EntityManager::create(
        [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite'
        ],
        $config
    );
});