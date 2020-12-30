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
 * Interface ArrayLogInterface
 * @package Acc\Core\Log
 */
interface ArrayLogInterface extends LogInterface
{
    /**
     * Creates an instance with appended entry
     * @param LogEntryInterface $entity
     * @return ArrayLogInterface
     */
    public function withEntry(LogEntryInterface $entity): ArrayLogInterface;

    /**
     * Creates an instance from serialized state to an array data
     * @param array $data
     * @return ArrayLogInterface
     */
    public function unserialized(array $data): ArrayLogInterface;

    /**
     * Creates an instance with init state
     * @return ArrayLogInterface
     */
    public function created(): ArrayLogInterface;

    /**
     * Defines a level below that entries will be filtering out
     * @param LogLevel $level
     * @return ArrayLogInterface
     */
    public function withMinLevel(LogLevel $level): ArrayLogInterface;
}
