<?php
declare(strict_types=1);

namespace PhpMud\Tests\Command;

class LevelTest extends CommandTest
{
    public function testLevel()
    {
        $client = $this->getMockClient();
        static::assertEquals('No debit levels available!', (string)$this->getCommands()->execute($client->input('level')));
        $client->getMob()->addExperience($client->getMob()->getExperienceToLevel() + 1);
        static::assertEquals('You level up! You are now level 2.', (string)$this->getCommands()->execute($client->input('level')));
        static::assertEquals('No debit levels available!', (string)$this->getCommands()->execute($client->input('level')));
    }
}
