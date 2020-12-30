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

use RuntimeException;

/**
 * Interface TextFileLogInterface
 * @package Acc\Core\Log
 */
interface TextFileLogInterface extends LogInterface
{
    /**
     * Opens logfile for writing
     * @return TextFileLogInterface
     * @throws RuntimeException
     */
    public function opened(): TextFileLogInterface;

    /**
     * Closes logfile
     * @return TextFileLogInterface
     * @throws RuntimeException
     */
    public function closed(): TextFileLogInterface;

    /**
     * Defines filename and the mode of opening file
     * @param string $pathname
     * @param string $mode
     * @return TextFileLogInterface
     * @throws RuntimeException
     */
    public function withFile(string $pathname, string $mode = "ab"): TextFileLogInterface;
}
