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

namespace PhpMud\Entity;

use PhpMud\Ability\AbilityFactory;
use PhpMud\Enum\Ability as AbilityEnum;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Ability
{
    use PrimaryKeyTrait;

    /** @ManyToOne(targetEntity="Mob", inversedBy="abilities") */
    protected $mob;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="integer") */
    protected $level;

    /** @var AbilityEnum */
    protected $enum;

    /** @var \PhpMud\Ability\Ability */
    protected $ability;

    public function __construct(Mob $mob, AbilityEnum $ability, int $level)
    {
        $this->mob = $mob;
        $this->name = $ability->getValue();
        $this->level = $level;
        $this->postLoad();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAbility(): \PhpMud\Ability\Ability
    {
        return $this->ability;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @PostLoad
     * @PostPersist
     */
    public function postLoad()
    {
        $this->enum = new AbilityEnum($this->name);
        $this->ability = AbilityFactory::newInstance($this->enum);
    }
}
