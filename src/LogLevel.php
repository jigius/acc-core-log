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

namespace Acc\Core\Log;

/**
 * Interface LogLevel
 * @package Acc\Core\Log
 */
final class LogLevel implements LogLevelInterface
{
    /**
     * @var int
     */
    private int $id;

    /**
     * LogLevel constructor.
     * @param int $id
     */
    public function __construct(int $id = LogLevelInterface::DEBUG)
    {
        $this->id = $id;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function toString(): string
    {
        switch ($this->id) {
            case LogLevelInterface::DEBUG:
                $ret = "DEBUG";
                break;
            case LogLevelInterface::INFO:
                $ret = "INFO";
                break;
            case LogLevelInterface::NOTICE:
                $ret = "NOTICE";
                break;
            case LogLevelInterface::WARNING:
                $ret = "WARNING";
                break;
            case LogLevelInterface::ERROR:
                $ret = "ERROR";
                break;
            case LogLevelInterface::CRITICAL:
                $ret = "CRITICAL";
                break;
            default:
                $ret = "UNKNOWN({$this->id})";
        }
        return $ret;
    }

    /**
     * @inheritDoc
     * @return int
     */
    public function toInt(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     * @param LogLevelInterface $t
     * @return bool
     */
    public function eq(LogLevelInterface $t): bool
    {
        return $this->id === $t->toInt();
    }

    /**
     * @inheritDoc
     * @param LogLevelInterface $t
     * @return bool
     */
    public function gt(LogLevelInterface $t): bool
    {
        return $this->id > $t->toInt();
    }

    /**
     * @inheritDoc
     * @param LogLevelInterface $t
     * @return bool
     */
    public function lt(LogLevelInterface $t): bool
    {
        return $this->id < $t->toInt();
    }
}
