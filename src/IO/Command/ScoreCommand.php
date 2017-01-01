<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ScoreCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['score'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    $mob = $input->getMob();
                    $inv = $mob->getInventory();
                    $attributes = $mob->getAttributes();

                    return new Output(
                        sprintf(
                            "You are %s. %d years old (%d hours).\n".
                            "You have %d of %d hit points, %d of %d mana, %d of %d moves.\n".
                            "Race: %s  Gender: %s  Class: %s  Kit: NA\n".
                            "Trains: %d  Practices: %d  Skill Points: %d  Bounty: 0\n".
                            "You are carrying %d/%d items, %d%% weight capacity.\n".
                            "Str: %d/%d Int: %d/%d Wis: %d/%d\n".
                            "Dex: %d/%d Con: %d/%d Cha: %d/%d\n".
                            "You have %d exp, %d gold, %d silver.\n".
                            "You need %d exp to level.\n",
                            $mob->getName(),
                            $mob->getAgeInYears(),
                            $mob->getAgeInHours(),
                            $mob->getHp(),
                            $mob->getAttribute('hp'),
                            $mob->getMana(),
                            $mob->getAttribute('mana'),
                            $mob->getMv(),
                            $mob->getAttribute('mv'),
                            (string)$mob->getRace(),
                            (string)$mob->getGender(),
                            (string)$mob->getJob(),
                            $mob->getTrains(),
                            $mob->getPractices(),
                            $mob->getSkillPoints(),
                            count($inv->getItems()),
                            $inv->getCapacityCount(),
                            $inv->getWeight() / $inv->getCapacityWeight(),
                            $attributes->getAttribute('str'),
                            $mob->getAttribute('str'),
                            $attributes->getAttribute('int'),
                            $mob->getAttribute('int'),
                            $attributes->getAttribute('wis'),
                            $mob->getAttribute('wis'),
                            $attributes->getAttribute('dex'),
                            $mob->getAttribute('dex'),
                            $attributes->getAttribute('con'),
                            $mob->getAttribute('con'),
                            $attributes->getAttribute('cha'),
                            $mob->getAttribute('cha'),
                            $mob->getExperience(),
                            $inv->getGold(),
                            $inv->getSilver(),
                            $mob->getExperienceToLevel()
                        )
                    );
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
