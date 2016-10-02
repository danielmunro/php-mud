<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

$container = new \League\Container\Container();

$container->add(
    \Doctrine\ORM\EntityManager::class,
    function () {
        $isDevMode = true;
        $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([__DIR__.'/src/Entity'], $isDevMode);

        return \Doctrine\ORM\EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path' => __DIR__ . '/db.sqlite'
            ],
            $config
        );
    }
);

$container->add(
    \PhpMud\Service\Direction::class,
    function () {
        return new \PhpMud\Service\Direction();
    }
);

$commandContainer = new \League\Container\Container();

$commandContainer->add(
    \PhpMud\Command\North::class,
    function () use ($container) {
        return new \PhpMud\Command\North($container->get(\PhpMud\Service\Direction::class));
    }
);

$commandContainer->add(
    \PhpMud\Command\South::class,
    function () use ($container) {
        return new \PhpMud\Command\South($container->get(\PhpMud\Service\Direction::class));
    }
);


$commandContainer->add(
    \PhpMud\Command\East::class,
    function () use ($container) {
        return new \PhpMud\Command\East($container->get(\PhpMud\Service\Direction::class));
    }
);


$commandContainer->add(
    \PhpMud\Command\West::class,
    function () use ($container) {
        return new \PhpMud\Command\West($container->get(\PhpMud\Service\Direction::class));
    }
);


$commandContainer->add(
    \PhpMud\Command\Up::class,
    function () use ($container) {
        return new \PhpMud\Command\Up($container->get(\PhpMud\Service\Direction::class));
    }
);


$commandContainer->add(
    \PhpMud\Command\Down::class,
    function () use ($container) {
        return new \PhpMud\Command\Down($container->get(\PhpMud\Service\Direction::class));
    }
);

$commandContainer->add(
    \PhpMud\Command\NewRoom::class,
    function () use ($container) {
        return new \PhpMud\Command\NewRoom($container->get(\PhpMud\Service\Direction::class));
    }
);

$commandContainer->add(
    \PhpMud\Command\Look::class,
    function () {
        return new \PhpMud\Command\Look();
    }
);

$container->add(
    'commands',
    $commandContainer
);
