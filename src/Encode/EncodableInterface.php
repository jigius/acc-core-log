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

/**
 * Interface EncodableInterface
 *
 * @package Acc\Core\Log\Encode
 */
interface EncodableInterface
{
    /**
     * Defines an input data
     * @param $v
     * @return EncodableInterface
     */
    public function withInput($v): EncodableInterface;

    /**
     * Returns a passed input data as an encoded string
     * @return string
     */
    public function encoded(): string;
}
