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

use Acc\Core\Log\Encode\JsonGzipBase64Encoded;
use Acc\Core\Registry\RegistryInterface;
use Acc\Core\Registry\Vanilla\Registry;
use Acc\Core\SerializableInterface;
use DateTime;
use DateTimeInterface;
use LogicException;
use DomainException;
use Throwable;

/**
 * Class LogExceptionEntry
 * @package Acc\Core\Log
 */
final class LogExceptionEntry implements LogExceptionEntryInterface, SerializableInterface
{
    /**
     * @var LogLevelInterface
     */
    private LogLevelInterface $level;

    /**
     * @var string|null
     */
    private ?string $text = null;

    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $dt = null;
    /**
     * Assigned attributes to the instance
     * @var Registry
     */
    private Registry $attrs;

    /**
     * LogEntry constructor.
     *
     * @param RegistryInterface|null $attrs
     */
    public function __construct(?RegistryInterface $attrs = null)
    {
        $this->level = new LogLevel(LogLevelInterface::DEBUG);
        $this->attrs = $attrs ?? new Registry();
    }

    /**
     * @inheritDoc
     */
    public function withException(Throwable $ex): self
    {
        $obj = $this->blueprinted();
        $obj->text =
            (new JsonGzipBase64Encoded())
                ->withInput(
                    $this->data($ex)
                )
                    ->encoded();
        $obj->dt = new DateTime();
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function withLevel(LogLevelInterface $level): self
    {
        $obj = $this->blueprinted();
        $obj->level = $level;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function level(): LogLevelInterface
    {
        return $this->level;
    }

    /**
     * @inheritDoc
     */
    public function serialized(): array
    {
        if ($this->text === null || $this->dt === null) {
            throw new LogicException("invalid state");
        }
        return [
            'level' => $this->level->toInt(),
            'dt' => $this->dt->format(DateTimeInterface::RFC3339_EXTENDED),
            'text' => $this->text
        ];
    }

    /**
     * @inheritDoc
     */
    public function unserialized(iterable $data): self
    {
        if (!isset($data['dt']) || !isset($data['level']) || !isset($data['text'])) {
            throw new DomainException("invalid data");
        }
        if (
            ($dt = DateTime::createFromFormat(DateTimeInterface::RFC3339_EXTENDED, $data['dt'])) === false &&
            ($dt = DateTime::createFromFormat(DateTimeInterface::ATOM, $data['dt'])) === false
        ) {
            throw new LogicException("data is corrupted");
        }
        $obj = $this->blueprinted();
        $obj->dt = DateTime::createFromFormat(DateTimeInterface::ATOM, $data['dt']);
        $obj->text = $data['text'];
        $obj->level = new LogLevel($data['level']);
        return $obj;
    }

    /**
     * @inheritdoc
     */
    public function withAttr(string $name, $val): self
    {
        $obj = $this->blueprinted();
        $obj->attrs = $this->attrs->updated($name, $val);
        return $obj;
    }

    /**
     * @inheritdoc
     */
    public function attrs(): RegistryInterface
    {
        return $this->attrs;
    }

    /**
     * @return $this
     */
    private function blueprinted():self
    {
        $obj = new self($this->attrs);
        $obj->dt = $this->dt;
        $obj->text = $this->text;
        $obj->level = $this->level;
        return $obj;
    }

    /**
     * @param Throwable $ex
     * @return array
     */
    private function data(Throwable $ex): array
    {
        return [
            'class' => get_class($ex),
            'code' => $ex->getCode(),
            'message' => $ex->getMessage(),
            'file' => $ex->getFile(),
            'line' => $ex->getLine(),
            'trace' => $ex->getTrace(),
            'previous' =>
                ($ex = $ex->getPrevious()) !== null?
                    $this->data($ex): null
        ];
    }
}
