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
use LogicException;

/**
 * Class ArrayLog
 * @package Core\Service\Log
 */
final class ArrayLog implements ArrayLogInterface, SerializableInterface
{
    /**
     * @var array
     */
    private array $ar;
    /**
     * @var LogLevelInterface
     */
    private LogLevelInterface $minLevel;
    /**
     * @var LogInterface
     */
    private $original;
    /**
     * @var ProcessableEntryInterface
     */
    private $p;

    /**
     * ArrayLog constructor.
     *
     * @param LogInterface|null $log
     * @param ProcessableEntryInterface|null $p
     */
    public function __construct(?LogInterface $log = null, ?ProcessableEntryInterface $p = null)
    {
        $this->ar = [];
        $this->original = $log ?? new NullLog();
        $this->p = $p ?? new VanillaProcessedEntry();
        $this->minLevel = new LogLevel(LogLevelInterface::INFO);
    }

    /**
     * @inheritDoc
     */
    public function withMinLevel(LogLevelInterface $level): self
    {
        $obj = $this->blueprinted();
        $obj->minLevel = $level;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function withEntry(LogEntryInterface $entity): self
    {
        $obj = $this->blueprinted();
        $obj->original = $this->original->withEntry($entity);
        if ($entity->level()->lt($this->minLevel)) {
            return $obj;
        }
        $obj->ar[] = $entity;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function serialized(): array
    {
        return [
            'ar' =>
                array_map(
                    function (LogEntryInterface $enty) {
                        return $enty->serialized();
                    },
                    $this->ar
                ),
            'minLevel' => $this->minLevel->toInt(),
            'original' => [
               'classname' => get_class($this->original),
                'state' => $this->original->serialized()
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function unserialized(iterable $data): self
    {
        if (
            !is_array($data) ||
            !isset($data['minLevel']) || !is_int($data['minLevel']) ||
            !isset($data['ar']) || !is_array($data['ar'])
        ) {
            throw new LogicException("type invalid");
        }
        if (isset($data['original'])) {
            if (
                !isset($data['original']['classname']) ||
                !is_string($data['original']['classname']) ||
                !class_exists($data['original']['classname']) ||
                !isset($data['original']['state']) ||
                !is_array($data['original']['state'])
            ) {
                throw new LogicException("type invalid");
            }
            $log = new $data['original']['classname']();
            if (!($log instanceof LogInterface)) {
                throw new LogicException("type invalid");
            }
            $log = $log->unserialized($data['original']['state']);
        } else {
            $log = new NullLog();
        }
        $obj = $this->blueprinted();
        $obj->original = $log;
        $obj->ar =
            array_map(
                function (array $itm) {
                    return (new LogTextEntry())->unserialized($itm);
                },
                $data['ar']
            );
        $obj->minLevel = new LogLevel($data['minLevel']);
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function created(): self
    {
        return new self($this->original);
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = $this->created();
        $obj->ar = $this->ar;
        $obj->minLevel = $this->minLevel;
        return $obj;
    }
}
