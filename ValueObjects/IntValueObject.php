<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects;

abstract class IntValueObject
{
    public function __construct(
        public readonly int $value,
    ) {}
}
