<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Item;
use PhpMud\Enum\Material;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CraftCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['craft'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getSubject()) {
                        return new Output('Format is: craft <material> <item name>');
                    }
                    try {
                        $material = new Material($input->getSubject());
                    } catch (\UnexpectedValueException $e) {
                        return new Output(sprintf('Unknown material, options are: %s.', Material::values()));
                    }

                    $item = new Item($input->getAssigningValue(), $material);
                    $input->getMob()->getInventory()->add($item);

                    return new Output(sprintf('You craft %s.', (string)$item));
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
