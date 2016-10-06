<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

$container = new \Pimple\Container();

$container[\Doctrine\ORM\EntityManager::class] = function () {
    $isDevMode = true;
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([__DIR__.'/src/Entity'], $isDevMode);

    return \Doctrine\ORM\EntityManager::create(
        [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite'
        ],
        $config
    );
};

$container[\PhpMud\Service\DirectionService::class] = function () {
    return new \PhpMud\Service\DirectionService();
};

$commands = new \Pimple\Container();

$commands[\PhpMud\Command\North::class] = $commands->protect(function () use ($container) {
        return new \PhpMud\Command\North($container[\PhpMud\Service\DirectionService::class]);
});

$commands[\PhpMud\Command\South::class] = $commands->protect(function () use ($container) {
    return new \PhpMud\Command\South($container[\PhpMud\Service\DirectionService::class]);
});

$commands[\PhpMud\Command\East::class] = $commands->protect(function () use ($container) {
    return new \PhpMud\Command\East($container[\PhpMud\Service\DirectionService::class]);
});

$commands[\PhpMud\Command\West::class] = $commands->protect(function () use ($container) {
    return new \PhpMud\Command\West($container[\PhpMud\Service\DirectionService::class]);
});

$commands[\PhpMud\Command\Up::class] = $commands->protect(function () use ($container) {
    return new \PhpMud\Command\Up($container[\PhpMud\Service\DirectionService::class]);
});

$commands[\PhpMud\Command\Down::class] = $commands->protect(function () use ($container) {
    return new \PhpMud\Command\Down($container[\PhpMud\Service\DirectionService::class]);
});

$commands[\PhpMud\Command\NewRoom::class] = $commands->protect(function () use ($container) {
    return new \PhpMud\Command\NewRoom($container[\PhpMud\Service\DirectionService::class]);
});

$commands[\PhpMud\Command\Look::class] = $commands->protect(function () {
    return new \PhpMud\Command\Look();
});

$commands[\PhpMud\Command\Quit::class] = $commands->protect(function (\PhpMud\Client $client) {
    return new \PhpMud\Command\Quit($client);
});

$container['commands'] = $commands;
