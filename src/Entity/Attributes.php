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

    protected $hp;

    protected $mana;

    protected $mv;

    protected $str;

    protected $int;

    protected $wis;

    protected $dex;

    protected $con;

    protected $cha;

    protected $hit;

    protected $dam;

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
        return $this->$attribute;
    }
}