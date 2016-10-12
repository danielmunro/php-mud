<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use PhpMud\ServiceProvider\Command\MoveCommand;
use PhpMud\ServiceProvider\Command\LookCommand;
use PhpMud\ServiceProvider\Command\NewRoomCommand;

use Pimple\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

$container = new Container();

$container[EntityManager::class] = function () {
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(
        [
            __DIR__.'/src/Entity'
        ],
        $isDevMode
    );

    return EntityManager::create(
        [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite'
        ],
        $config
    );
};

$commands = new Container();

$commands->register(new MoveCommand());
$commands->register(new LookCommand());
$commands->register(new NewRoomCommand());

$container['commands'] = $commands;
