<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use PhpMud\ServiceProvider\Command\MoveCommand;
use PhpMud\ServiceProvider\Command\LookCommand;
use PhpMud\ServiceProvider\Command\NewRoomCommand;
use PhpMud\ServiceProvider\Command\QuitCommand;

use Pimple\Container;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

$configFile = __DIR__.'/config.yaml';

if (!file_exists($configFile)) {
    die('need a config file');
}

$config = Yaml::parse(file_get_contents($configFile));

$container = new Container();

$container[EntityManager::class] = function () use ($config) {

    return EntityManager::create(
        [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite'
        ],
        Setup::createAnnotationMetadataConfiguration(
            [
                __DIR__.'/src/Entity'
            ],
            $config['debug']
        )
    );
};

$commands = new Container();

$commands->register(new MoveCommand());
$commands->register(new LookCommand());
$commands->register(new NewRoomCommand());
$commands->register(new QuitCommand());

$container['commands'] = $commands;
