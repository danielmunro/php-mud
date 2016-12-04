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

abstract class Color
{
    public static function cyan(string $message): string
    {
        return sprintf("\e[36m%s\e[0m", $message);
    }

    public static function white(string $message): string
    {
        return sprintf("\e[1;37m%s\e[0m", $message);
    }

    public static function green(string $message): string
    {
        return sprintf("\e[32m%s\e[0m", $message);
    }
}