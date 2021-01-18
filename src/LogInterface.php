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
 * Interface LogInterface
 * @package Acc\Core\Log
 */
interface LogInterface
{
    /**
     * Creates an instance with appended entry
     * @param LogEntryInterface $entity
     * @return LogInterface
     */
    public function withEntry(LogEntryInterface $entity): LogInterface;

    /**
     * Creates an instance with init state
     * @return LogInterface
     */
    public function created(): LogInterface;

    /**
     * Defines a level below that entries will be filtering out
     * @param LogLevelInterface $level
     * @return LogInterface
     */
    public function withMinLevel(LogLevelInterface $level): LogInterface;
}
