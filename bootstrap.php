<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

$configFile = __DIR__.'/config.yaml';

if (!file_exists($configFile)) {
    die('need a config file');
}

$config = Yaml::parse(file_get_contents($configFile));

$em = EntityManager::create(
    $config['persist'],
    Setup::createAnnotationMetadataConfiguration(
        [
            __DIR__.'/src/Entity'
        ],
        $config['debug']
    )
);
