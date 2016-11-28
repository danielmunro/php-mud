<?php
declare(strict_types=1);

/**
 * This file is part of the PhpMud package.
 *
 * (c) Dan Munro <dan@danmunro.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpMud;

use PhpMud\Entity\Mob;
use PhpMud\Enum\Gender;
use PhpMud\Enum\Race;
use PhpMud\IO\Input;

class Login
{
    const STATE_NAME = 'name';

    const STATE_RACE = 'race';

    const STATE_GENDER = 'gender';

    const STATE_COMPLETE = 'complete';

    /**
     * @var int
     */
    protected $state;

    /**
     * @var string
     */
    protected $mobName;

    /**
     * @var Mob
     */
    protected $mob;

    public function __construct()
    {
        $this->state = static::STATE_NAME;
    }

    public function next(Input $input): string
    {
        switch ($this->state) {
            case static::STATE_NAME:
                $this->mobName = (string) $input;
                $input->getClient()->write('Ok. Pick a race > ');
                $this->state = static::STATE_RACE;
                break;
            case static::STATE_RACE:
                try {
                    $this->mob = new Mob($this->mobName, new Race((string)$input));
                    $input->getClient()->write('Ok. Optionally, pick a gender (female/male/neutral) > ');
                    $this->state = static::STATE_GENDER;
                } catch (\UnexpectedValueException $e) {
                    $input->getClient()->write("That's not a valid race. Try again > ");
                }
                break;
            case static::STATE_GENDER:
                $gender = Gender::partialSearch((string) $input);

                if ($gender) {
                    $this->mob->setGender($gender);
                    $input->getClient()->write("Done.\n");
                    $this->state = static::STATE_COMPLETE;
                } elseif ($input->getCommand()) {
                    $input->getClient()->write('Not understood, try again (female/male/neutral) > ');
                } else {
                    $input->getClient()->write("Done.\n");
                    $this->state = static::STATE_COMPLETE;
                }
                break;
        }

        return $this->state;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getMob(): Mob
    {
        return $this->mob;
    }
}
