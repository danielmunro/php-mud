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

use PhpMud\Enum\Ability as AbilityEnum;

/**
 * @Entity
 */
class Ability
{
    use PrimaryKeyTrait;

    /** @ManyToOne(targetEntity="Mob", inversedBy="abilities") */
    protected $mob;

    /** @Column(type="string") */
    protected $ability;

    /** @Column(type="integer") */
    protected $level;

    public function __construct(Mob $mob, AbilityEnum $ability, int $level)
    {
        $this->mob = $mob;
        $this->ability = $ability;
        $this->level = $level;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function checkImprovement()
    {
    }

    /**
     * @PostLoad
     * @PostPersist
     */
    public function postLoad()
    {
        $this->ability = AbilityEnum::fromName((string)$this->ability);
    }

    /**
     * @PrePersist
     */
    public function prePersist()
    {
        $this->ability = (string)$this->ability;
    }
}
