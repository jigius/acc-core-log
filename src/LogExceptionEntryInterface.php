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

use Throwable;

/**
 * Interface LogExceptionEntryInterface
 * @package Acc\Core\Log
 */
interface LogExceptionEntryInterface extends LogEntryInterface
{
    /**
     * @param Throwable $ex
     * @return LogExceptionEntryInterface
     */
    public function withException(Throwable $ex): LogExceptionEntryInterface;
}
