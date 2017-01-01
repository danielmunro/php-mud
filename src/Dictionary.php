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

use function Functional\filter;

class Dictionary
{
    protected static $functionWords = [
        'i',
        'an',
        'in',
        'it',
        'a',
        'the'
    ];

    public static function nonFunctionWords(string $message): array
    {
        return filter(
            explode(' ', $message),
            function (string $word) {
                return !in_array($word, static::$functionWords, true);
            }
        );
    }
}
