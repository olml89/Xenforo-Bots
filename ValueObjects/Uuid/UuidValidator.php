<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Uuid;

interface UuidValidator
{
    public function isValid(string $uuid): bool;
}
