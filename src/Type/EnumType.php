<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

use Serafim\BinStream\Stream\ReadableStreamInterface;
use Serafim\BinStream\Stream\WritableStreamInterface;

/**
 * @template T of \BackedEnum
 * @template-extends Type<T>
 */
class EnumType extends Type
{
    /**
     * @var IntType
     */
    public readonly IntType $type;

    /**
     * @param class-string<T> $enum
     * @param IntType|class-string<IntType> $type
     */
    public function __construct(
        public readonly string $enum,
        IntType|string $type = new UInt32Type()
    ) {
        $this->type = \is_string($type) ? new $type() : $type;

        parent::__construct($this->type->size);
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): \BackedEnum
    {
        $value = $this->type->parse($stream);

        return ($this->enum)::from($value);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert($data instanceof \BackedEnum, new \InvalidArgumentException(
            'Expected instance of BackedEnum, but ' . \get_debug_type($data) . ' given'
        ));

        return $this->type->serialize($data->value, $stream);
    }
}
