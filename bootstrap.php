<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Monolog\Logger;

$configFile = __DIR__.'/config.yaml';

if (!file_exists($configFile)) {
    die(
<<<NOCONFIG
No configuration file was found!

Create a file at ./config.yaml with example contents below:

ip: 127.0.0.1
port: 9000
debug: true
persist:
  driver: pdo_sqlite
  path: db.sqlite
log:
  name: phpmud

NOCONFIG
);
}

$config = Yaml::parse(file_get_contents($configFile));

$log = new Logger($config['log']['name']);

$em = EntityManager::create(
    $config['persist'],
    Setup::createAnnotationMetadataConfiguration(
        [
            __DIR__.'/src/Entity'
        ],
        $config['debug']
    )
);
