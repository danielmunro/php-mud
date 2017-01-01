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

use PhpMud\Enum\Material;
use PhpMud\Enum\Position;
use PhpMud\Noun;
use Ramsey\Uuid\Uuid;

/**
 * @Entity
 * @Table(indexes={@Index(name="vnum_idx", columns={"vNum"})})
 * @HasLifecycleCallbacks
 */
class Item implements Noun
{
    use PrimaryKeyTrait;
    use VNumTrait;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string", nullable=true) */
    protected $look;

    /** @Column(type="string") */
    protected $material;

    /** @Column(type="array") */
    protected $identifiers;

    /** @Column(type="decimal") */
    protected $weight;

    /** @Column(type="decimal") */
    protected $value;

    /** @ManyToOne(targetEntity="Inventory", inversedBy="items") */
    protected $inventory;

    /** @Column(type="string", nullable=true) */
    protected $position;

    /** @Column(type="integer") */
    protected $level;

    /** @ManyToOne(targetEntity="Mob") */
    protected $craftedBy;

    public function __construct(
        string $name,
        Material $material,
        array $identifiers,
        float $weight = 0.0,
        float $value = 0.0
    ) {
        $this->name = $name;
        $this->material = $material;
        $this->identifiers = $identifiers;
        $this->weight = $weight;
        $this->value = $value;
        $this->level = 1;
        $this->vNum = Uuid::uuid4()->toString();
    }

    public function setInventory(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLook(): string
    {
        return $this->look ?? 'is here.';
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition(Position $position)
    {
        $this->position = $position;
    }

    public function getWeight(): float
    {
        return (float)$this->weight;
    }

    public function setWeight(float $weight)
    {
        $this->weight = $weight;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value)
    {
        $this->value = $value;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level)
    {
        $this->level = $level;
    }

    public function getCraftedBy(): Mob
    {
        return $this->craftedBy;
    }

    public function setCraftedBy(Mob $craftedBy)
    {
        $this->craftedBy = $craftedBy;
    }

    public function getLongDescription(): string
    {
        return $this->look ?? '%s is here.';
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @PostLoad
     * @PostPersist
     */
    public function postLoad()
    {
        if ($this->position) {
            $this->position = new Position($this->position);
        }
        $this->value = (float)$this->value;
    }

    /**
     * @PrePersist
     */
    public function prePersist()
    {
        $this->position = (string) $this->position;
    }
}
