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

namespace Acc\Core\Log\Encode;

use Acc\Core\Value\ValueInterface;
use Acc\Core\Value\Vanilla\StaticValue;
use RuntimeException;

/**
 * Class JsonGzipBase64Encoded
 *
 * Encodes data via chain Json-Gzip-Base64
 *
 * @package Acc\Core\Log\Encode
 */
final class JsonGzipBase64Encoded implements EncodableInterface
{
    /**
     * @var ValueInterface|StaticValue
     */
    private ValueInterface $i;

    /**
     * JsonGzipBase64Encoded constructor.
     */
    public function __construct()
    {
        $this->i = new StaticValue();
    }

    /**
     * @inheritdoc
     */
    public function withInput($v): self
    {
        $obj = $this->blueprinted();
        $obj->i = $obj->i->assigned($v);
        return $obj;
    }

    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function encoded(): string
    {
        $res =
            json_encode(
                $this->i->fetch(),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE
            );
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw
                new RuntimeException(
                    "Couldn't encode exception with `json_encode`",
                    0,
                    new RuntimeException(json_last_error_msg(), json_last_error())
                );
        }
        if (function_exists('gzencode')) {
            error_clear_last();
            /** @noinspection PhpComposerExtensionStubsInspection */
            $res = gzencode($res);
            if ($res === false) {
                throw
                    new RuntimeException(
                        "Couldn't encode a text with `gzencode` - `false` is returned",
                        0,
                        new RuntimeException(error_get_last()['message'] ?? "unknown error :(")
                    );
            }
        }
        return base64_encode($res);
    }

    /**
     * Clones the instance
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self();
        $obj->i = $this->i;
        return $obj;
    }
}
