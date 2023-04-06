<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects;

use Stringable;

abstract class StringValueObject implements Stringable
{
    public function __construct(
        private readonly string $value,
    ) {}

    public function __toString(): string
    {
        return $this->value;
    }
}
