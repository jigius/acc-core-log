<?php
/**
 * This file is part of the jigius/acc-core-log library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2021 Jigius <jigius@gmail.com>
 * @link https://github.com/jigius/acc-core-log GitHub
 */

declare(strict_types=1);

namespace Acc\Core\Log;

/**
 * Interface ProcessableEntryInterface
 *
 * Transforms an entity into a string
 *
 * @package Acc\Core\Log
 */
interface ProcessableEntryInterface
{
    /**
     * Does a transformation
     * @param LogEntryInterface $entry
     * @return string
     */
    public function entry(LogEntryInterface $entry): string;
}
