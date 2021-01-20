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

use LogicException;

/**
 * Class VanillaProcessedEntry
 *
 * Realizes a base way for the log entries formatting
 * @package Acc\Core\Log
 */
final class VanillaProcessedEntry implements ProcessableEntryInterface
{
    /**
     * VanillaProcessedEntry constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritdoc
     * @throws LogicException
     */
    public function entry(LogEntryInterface $entry): string
    {
        $i = $entry->serialized();
        if (!isset($i['dt']) || !isset($i['level']) || !isset($i['text'])) {
            throw new LogicException("invalid type");
        }
        return
            sprintf(
                "%s\t%s\t%s\n",
                $i['dt'],
                str_pad(
                    $entry->level()->toString(),
                    7,
                    " ",
                    STR_PAD_LEFT
                ),
                $i['text']
            );
    }
}
