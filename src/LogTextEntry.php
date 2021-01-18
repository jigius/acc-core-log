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

use Acc\Core\AttributableInterface;
use Acc\Core\Registry\RegistryInterface;
use Acc\Core\Registry\Vanilla\Registry;
use Acc\Core\SerializableInterface;
use DateTime;
use DateTimeInterface;
use DomainException;
use LogicException;

/**
 * Class LogTextEntry
 * @package Acc\Core\Log
 */
final class LogTextEntry implements LogTextEntryInterface, SerializableInterface
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
     * LogTextEntry constructor.
     */
    public function __construct(?RegistryInterface $attrs = null)
    {
        $this->level = new LogLevel(LogLevelInterface::INFO);
        $this->attrs = new Registry();
    }

    /**
     * @inheritDoc
     */
    public function withText(string $text): self
    {
        $obj = $this->blueprinted();
        $obj->text = $text;
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
     * @throws LogicException
     */
    public function serialized(): array
    {
        if ($this->text === null || $this->dt === null) {
            throw new LogicException("the text isn't was specified");
        }
        return [
            'level' => $this->level->toInt(),
            'dt' => $this->dt->format(DateTimeInterface::ATOM),
            'text' => $this->text
        ];
    }

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function unserialized(iterable $data): self
    {
        if (
            !is_array($data) ||
            !isset($data['dt']) ||
            !isset($data['level']) ||
            !isset($data['text'])
        ) {
            throw new DomainException("invalid data");
        }
        $obj = $this->blueprinted();
        if (($dt = DateTime::createFromFormat(DateTimeInterface::ATOM, $data['dt'])) === false) {
            throw new LogicException("data is corrupted");
        }
        $obj->dt = $dt;
        $obj->text = $data['text'];
        $obj->level = new LogLevel($data['level']);
        return $obj;
    }

    public function withAttr(string $name, $val): AttributableInterface
    {
        $obj = $this->blueprinted();
        $obj->attrs = $this->attrs->
    }

    public function attrs(): RegistryInterface
    {
        // TODO: Implement attrs() method.
    }

    /**
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self();
        $obj->dt = $this->dt;
        $obj->text = $this->text;
        $obj->level = $this->level;
        return $obj;
    }
}
