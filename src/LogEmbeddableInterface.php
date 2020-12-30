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
 * Interface LogEmbeddableInterface
 *
 * @package Acc\Core\Log
 */
interface LogEmbeddableInterface
{
    /**
     * Defines an embedded log instance
     * @param LogInterface $log
     * @return LogInterface
     */
    public function withEmbedded(LogInterface $log): LogInterface;
}
