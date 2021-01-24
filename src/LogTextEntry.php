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
     * Assigned attributes to the instance
     * @var Registry
     */
    private Registry $attrs;

    /**
     * LogTextEntry constructor.
     *
     * @param RegistryInterface|null $attrs
     */
    public function __construct(?RegistryInterface $attrs = null)
    {
        $this->level = new LogLevel(LogLevelInterface::INFO);
        $this->attrs = $attrs ?? new Registry();
    }

    /**
     * @inheritDoc
     */
    public function withText(string $text): self
    {
        $obj = $this->blueprinted();
        $obj->text =
            str_replace(
                "\r",
                "\\r",
                str_replace(
                    "\n",
                    "\\n",
                    $text
                )
            );
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
            'dt' => $this->dt->format(DateTimeInterface::RFC3339_EXTENDED),
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
        if (
            ($dt = DateTime::createFromFormat(DateTimeInterface::RFC3339_EXTENDED, $data['dt'])) === false &&
            ($dt = DateTime::createFromFormat(DateTimeInterface::ATOM, $data['dt'])) === false
        ) {
            throw new LogicException("data is corrupted");
        }
        $obj = $this->blueprinted();
        $obj->dt = $dt;
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
    private function blueprinted(): self
    {
        $obj = new self($this->attrs);
        $obj->dt = $this->dt;
        $obj->text = $this->text;
        $obj->level = $this->level;
        return $obj;
    }
}
