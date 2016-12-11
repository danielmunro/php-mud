<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;
use function Functional\with;

class HelpCommand implements ServiceProviderInterface
{
    private static $helpFuncs = [
        'dwarf'
    ];

    public function register(Container $pimple)
    {
        $pimple['help'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return HelpCommand::getHelpOutput($input->getSubject());
                }
            };
        });
    }

    public static function getHelpOutput(string $subject): Output
    {
        return with(
            first(self::$helpFuncs, function (string $helpFunc) use ($subject) {
                return strpos($helpFunc, $subject) === 0;
            }),
            function(string $helperFunc) {
                $func = sprintf('\PhpMud\Help\%s', $helperFunc);

                if (function_exists($func)) {
                    return new Output($func());
                }
            }
        ) ?? new Output("That help topic does not exist.\n");
    }
}
