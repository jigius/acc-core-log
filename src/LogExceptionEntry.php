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
use DateTime;
use DateTimeInterface;
use RuntimeException;
use LogicException;
use DomainException;
use Exception;
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
     * LogEntry constructor.
     */
    public function __construct()
    {
        $this->level = new LogLevel(LogLevelInterface::DEBUG);
    }

    /**
     * @inheritDoc
     * @return LogExceptionEntryInterface
     * @throws Exception
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function withException(Throwable $ex): LogExceptionEntryInterface
    {
        $text =
            json_encode(
                $this->data($ex),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw
                new DomainException(
                    "Couldn't encode exception with `json_encode`",
                    new RuntimeException(json_last_error_msg(), json_last_error())
                );
        }
        if (function_exists('gzencode')) {
            $text = gzencode($text);
            if ($text === false) {
                throw
                    new DomainException(
                        "Couldn't encode exception with `gzencode` - `false` is returned"
                    );
            }
        }
        $obj = $this->blueprinted();
        $obj->text = base64_encode($text);
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
            'dt' => $this->dt->format(DateTimeInterface::ATOM),
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
        $obj = $this->blueprinted();
        $obj->dt = DateTime::createFromFormat(DateTimeInterface::ATOM, $data['dt']);
        $obj->text = $data['text'];
        $obj->level = new LogLevel($data['level']);
        return $obj;
    }

    /**
     * @return $this
     */
    private function blueprinted():self
    {
        $obj = new self();
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
