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

use Acc\Core\SerializableInterface;

/**
 * Class NullLog
 * It does not store log entries - just a stub
 *
 * @package Acc\Core\Log
 */
final class NullLog implements LogInterface, SerializableInterface
{
    /**
     * NullLog constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function withEntry(LogEntryInterface $entity): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function serialized(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function unserialized(iterable $data): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function created(): self
    {
        return new self();
    }

    /**
     * @inheritDoc
     */
    public function withMinLevel(LogLevelInterface $level): self
    {
        return $this;
    }
}
