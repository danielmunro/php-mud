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

use function Functional\each;

/**
 * @Entity
 */
class Attributes
{
    use PrimaryKeyTrait;

    /** @ORM\Column(type="integer") */
    protected $hp;

    /** @ORM\Column(type="integer") */
    protected $mana;

    /** @ORM\Column(type="integer") */
    protected $mv;

    /** @ORM\Column(type="integer") */
    protected $str;

    /** @ORM\Column(type="integer") */
    protected $int;

    /** @ORM\Column(type="integer") */
    protected $wis;

    /** @ORM\Column(type="integer") */
    protected $dex;

    /** @ORM\Column(type="integer") */
    protected $con;

    /** @ORM\Column(type="integer") */
    protected $cha;

    /** @ORM\Column(type="integer") */
    protected $hit;

    /** @ORM\Column(type="integer") */
    protected $dam;

    /** @ORM\Column(type="integer") */
    protected $acSlash;

    /** @ORM\Column(type="integer") */
    protected $acBash;

    /** @ORM\Column(type="integer") */
    protected $acPierce;

    /** @ORM\Column(type="integer") */
    protected $acMagic;

    public function __construct(array $attributes = [])
    {
        if ($attributes) {
            $this->setAttributes($attributes);
        }
    }

    public function setAttributes(array $attributes)
    {
        each(
            $attributes,
            function (int $value, string $attribute) {
                $this->$attribute = $value;
            }
        );
    }

    public function modifyAttribute(string $attribute, int $amount)
    {
        $this->$attribute += $amount;
    }

    public function getAttribute(string $attribute): int
    {
        return $this->$attribute ?? 0;
    }
}
