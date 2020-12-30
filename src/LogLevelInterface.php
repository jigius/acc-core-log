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
 * Interface LogLevel
 * @package Acc\Core\Log
 */
interface LogLevelInterface
{
    public const DEBUG = 0;
    public const INFO = 1;
    public const NOTICE = 2;
    public const WARNING = 3;
    public const ERROR = 4;
    public const CRITICAL = 5;

    /**
     *  Returns human description of a level
     * @return string
     */
    public function toString(): string;

    /**
     * Returns numeric id of a level
     * @return int
     */
    public function toInt(): int;

    /**
     * Tests if the instance is greater then tested one
     * @param LogLevelInterface $t
     * @return bool
     */
    public function gt(LogLevelInterface $t): bool;

    /**
     * Tests if the instance is lesser then tested one
     * @param LogLevelInterface $t
     * @return bool
     */
    public function lt(LogLevelInterface $t): bool;

    /**
     * Tests if the instance is equal to tested one
     * @param LogLevelInterface $t
     * @return bool
     */
    public function eq(LogLevelInterface $t): bool;
}
