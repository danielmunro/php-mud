<?php
declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__.'/bootstrap.php';

$entityManager = $container->get(\PhpMud\Enum\ContainerService::EM);

return ConsoleRunner::createHelperSet($entityManager);