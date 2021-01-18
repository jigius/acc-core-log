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
use DomainException;
use LogicException;

/**
 * Class LimitedArrayLog
 *
 * @package Acc\Core\Log
 */
final class LimitedArrayLog implements ArrayLogInterface, SerializableInterface
{
    /**
     * @var ArrayLogInterface
     */
    private ArrayLogInterface $original;
    /**
     * @var int
     */
    private int $limit;

    /**
     * LimitedArrayLog constructor.
     *
     * @param ArrayLogInterface $l
     * @param int $limit
     */
    public function __construct(ArrayLogInterface $l, int $limit)
    {
        $this->original = $l;
        $this->limit = $limit;
    }

    /**
     * @inheritDoc
     */
    public function withEntry(LogEntryInterface $entity): ArrayLogInterface
    {
        $obj = $this->blueprinted();
        $obj->original = $this->original->withEntry(($entity));
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function withMinLevel(LogLevelInterface $level): self
    {
        $obj = $this->blueprinted();
        $obj->original = $this->original->withMinLevel($level);
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function serialized(): array
    {
        if ($this->limit <= 0) {
            throw new DomainException("invalid value for param `limit`");
        }
        $i = $this->original->serialized();
        if (!isset($i['minLevel']) || !isset($i['ar']) || !is_array($i['ar'])) {
            throw new DomainException("data invalid");
        }
        $i['ar'] = array_slice($i['ar'], $this->limit * -1);
        return [
            'classname' => get_class($this->original),
            'state' => $i
        ];
    }

    /**
     * @inheritDoc
     */
    public function unserialized(iterable $data): self
    {
        if (
            !is_array($data) ||
            !isset($data['classname']) ||
            !isset($data['state']) ||
            !is_array($data['state'])
        ) {
            throw new DomainException("data invalid");
        }
        $orig = new $data['classname']();
        if (!($orig instanceof ArrayLogInterface)) {
            throw new LogicException("invalid type");
        }
        $obj = $this->blueprinted();
        $obj->original = $orig->unserialized($data['state']);
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function created(): self
    {
        return new self($this->original->created(), $this->limit);
    }

    /**
     * @return $this
     */
    private function blueprinted(): self
    {
        return $this->created();
    }
}
