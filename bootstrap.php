<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use PhpMud\ServiceProvider\Command\NorthCommand;
use PhpMud\ServiceProvider\Command\SouthCommand;
use PhpMud\ServiceProvider\Command\EastCommand;
use PhpMud\ServiceProvider\Command\WestCommand;
use PhpMud\ServiceProvider\Command\UpCommand;
use PhpMud\ServiceProvider\Command\DownCommand;
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

$commands->register(new NorthCommand());
$commands->register(new SouthCommand());
$commands->register(new EastCommand());
$commands->register(new WestCommand());
$commands->register(new UpCommand());
$commands->register(new DownCommand());
$commands->register(new LookCommand());
$commands->register(new NewRoomCommand());

$container['commands'] = $commands;
