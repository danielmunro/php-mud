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
use PhpMud\IO\Input;
use PhpMud\Job\Job;
use PhpMud\Race\Race;
use PhpMud\Repository\MobRepository;

class Login
{
    const STATE_NAME = 'name';

    const STATE_RACE = 'race';

    const STATE_JOB = 'job';

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
     * @var Race
     */
    protected $race;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var Mob
     */
    protected $mob;

    /**
     * @var MobRepository
     */
    protected $mobRepository;

    public function __construct(MobRepository $mobRepository)
    {
        $this->state = static::STATE_NAME;
        $this->mobRepository = $mobRepository;
    }

    public function next(Input $input): string
    {
        switch ($this->state) {
            case static::STATE_NAME:
                $this->mobName = (string) $input;
                $this->mob = $this->mobRepository->findOneBy([
                    'name' => $this->mobName
                ]);
                if ($this->mob) {
                    $this->state = static::STATE_COMPLETE;
                    $input->getClient()->write("Welcome back.\n");
                    break;
                }
                $this->state = static::STATE_RACE;
                $input->getClient()->write('Ok. Pick a race > ');
                break;
            case static::STATE_RACE:
                if (!$input->getCommand()) {
                    $input->getClient()->write('Please pick a race > ');
                    break;
                }
                try {
                    $this->race = Race::matchPartialValue((string)$input);
                    $input->getClient()->write('Ok. Pick a job [warrior/cleric/mage/thief] > ');
                    $this->state = static::STATE_JOB;
                } catch (\UnexpectedValueException $e) {
                    $input->getClient()->write("That's not a valid race. Try again > ");
                }
                break;
            case static::STATE_JOB:
                try {
                    $this->job = Job::matchPartialValue((string)$input);
                    $this->mob = new Mob($this->mobName, $this->race);
                    $this->mob->setJob($this->job);
                    $this->mob->getInventory()->modifySilver(20);
                    $input->getClient()->write('Ok. Optionally, pick a gender (female/male/neutral) > ');
                    $this->state = static::STATE_GENDER;
                } catch (\UnexpectedValueException $e) {
                    $input->getClient()->write("That's not a valid job. Try again > ");
                }
                break;
            case static::STATE_GENDER:
                if ($input->getCommand()) {
                    $gender = Gender::partialSearch((string)$input);
                    if ($gender) {
                        $this->mob->setGender($gender);
                        $input->getClient()->write("Done.\n");
                        $this->state = static::STATE_COMPLETE;
                        break;
                    }

                    $input->getClient()->write('Not understood, try again (female/male/neutral) > ');
                    break;
                }

                $input->getClient()->write("Done.\n");
                $this->state = static::STATE_COMPLETE;
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
