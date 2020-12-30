<?php
/**
 * This file is part of the jigius/acc-core-log library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2020 Jigius <jigius@gmail.com>
 * @link https://github.com/jigius/acc-core-log GitHub
 */

declare(strict_types=1);

namespace Acc\Core\Log;

/**
 * Interface LogEntryInterface
 * @package Acc\Core\Log
 */
interface LogEntryInterface
{
    /**
     * Defines entry's level
     * @param LogLevelInterface $level
     * @return mixed
     */
    public function withLevel(LogLevelInterface $level);

    /**
     * Returns the current level
     * @return LogLevelInterface
     */
    public function level(): LogLevelInterface;
}
