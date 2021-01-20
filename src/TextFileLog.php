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
use RuntimeException;
use LogicException;
use DomainException;

/**
 * Class TextFileLog
 * @package Acc\Core\Log
 */
final class TextFileLog implements TextFileLogInterface, SerializableInterface, LogEmbeddableInterface
{
    /**
     * @var resource|null
     */
    private $fd;
    /**
     * @var array
     */
    private array $i;
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
     * TextFileLog constructor.
     *
     * @param LogInterface|null $log
     * @param ProcessableEntryInterface|null $p
     */
    public function __construct(?LogInterface $log = null, ?ProcessableEntryInterface $p = null)
    {
        $this->i = [
            'mode' => "ab"
        ];
        $this->original = $log ?? new NullLog();
        $this->p = $p ?? new VanillaProcessedEntry();
        $this->fd = null;
        $this->minLevel = new LogLevel(LogLevelInterface::INFO);
    }

    /**
     * @inheritDoc
     */
    public function withEntry(LogEntryInterface $entity): self
    {
        $obj = $this->blueprinted();
        if ($entity->level()->lt($this->minLevel)) {
            $obj->original = $this->original->withEntry($entity);
            return $obj;
        }
        if (!$this->fd) {
            return $obj->opened()->withEntry($entity);
        }
        $obj->original = $this->original->withEntry($entity);
        $i = $entity->serialized();
        if (!isset($i['dt']) || !isset($i['level']) || !isset($i['text'])) {
            throw new DomainException("invalid data");
        }
        $line = $this->p->entry($entity);
        error_clear_last();
        if (@fwrite($this->fd, $line) === false) {
            throw new RuntimeException(
                sprintf(
                    "Couldn't write into file=`%s`: %s",
                    $this->i['pathname'],
                    error_get_last()['message'] ?? "unknown error :("
                )
            );
        }
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function serialized(): array
    {
        return [
            'i' => $this->i,
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
            !isset($data['i']) || !is_array($data['i']) ||
            !isset($data['original']['classname']) || !is_string($data['original']['classname']) ||
            !class_exists($data['original']['classname']) ||
            !isset($data['original']['state']) || !is_array($data['original']['state'])
        ) {
            throw new LogicException("type invalid");
        }
        $log = new $data['original']['classname']();
        if (!($log instanceof LogInterface)) {
            throw new LogicException("type invalid");
        }
        $obj = $this->blueprinted();
        $obj->original = $log->unserialized($data['original']['state']);
        $obj->minLevel = new LogLevel($data['minLevel']);
        $obj->i = $data['i'];
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
        $obj->i = $this->i;
        $obj->minLevel = $this->minLevel;
        $obj->fd = $this->fd;
        return $obj;
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     * @throws LogicException
     */
    public function opened(): self
    {
        if ($this->fd) {
            return $this;
        }
        if (empty($this->i['pathname'])) {
            throw new LogicException("`pathname` is not defined");
        }
        if (strncmp($this->i['pathname'], "php://", 6) !== 0) {
            $folder = dirname($this->i['pathname']);
            if (!empty($folder) && !is_dir($folder)) {
                if (@mkdir($folder, 0755, true) === false) {
                    throw new RuntimeException(
                        sprintf(
                            "couldn't create folder=`%s`: %s",
                            $folder,
                            error_get_last()['message'] ?? "null"
                        )
                    );
                }
            }
        }
        $fd = @fopen($this->i['pathname'], $this->i['mode']);
        if ($fd === false) {
            throw new RuntimeException(
                sprintf(
                    "couldn't open file=`%s` with mode=`%s`: %s",
                    $this->i['pathname'],
                    $this->i['mode'],
                    error_get_last()['message'] ?? "null"
                )
            );
        }
        $obj = $this->blueprinted();
        $obj->fd = $fd;
        return $obj;
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     */
    public function closed(): self
    {
        if (!$this->fd) {
            return $this;
        }
        if (@fclose($this->fd) === false) {
            throw new RuntimeException(
                sprintf(
                    "couldn't close file=`%s`: %s",
                    $this->i['pathname'],
                    error_get_last()['message'] ?? "null"
                )
            );
        }
        $obj = $this->blueprinted();
        $obj->fd = null;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function withFile(string $pathname, string $mode = "ab"): self
    {
        if ($this->fd) {
            throw new LogicException("is opened already");
        }
        $obj = $this->blueprinted();
        $obj->i['pathname'] = $pathname;
        $obj->i['mode'] = $mode;
        return $obj;
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
    public function withEmbedded(LogInterface $log): self
    {
        if ($this->original instanceof LogEmbeddableInterface) {
            $obj = $this->original->withEmbedded($log);
        } else {
            $obj = $this->blueprinted();
            $obj->original = $log;
        }
        return $obj;
    }
}
