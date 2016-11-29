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

    /** @Column(type="integer") */
    protected $hp;

    /** @Column(type="integer") */
    protected $mana;

    /** @Column(type="integer") */
    protected $mv;

    /** @Column(type="integer") */
    protected $str;

    /** @Column(type="integer") */
    protected $int;

    /** @Column(type="integer") */
    protected $wis;

    /** @Column(type="integer") */
    protected $dex;

    /** @Column(type="integer") */
    protected $con;

    /** @Column(type="integer") */
    protected $cha;

    /** @Column(type="integer") */
    protected $hit;

    /** @Column(type="integer") */
    protected $dam;

    /** @Column(type="integer") */
    protected $acSlash;

    /** @Column(type="integer") */
    protected $acBash;

    /** @Column(type="integer") */
    protected $acPierce;

    /** @Column(type="integer") */
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
