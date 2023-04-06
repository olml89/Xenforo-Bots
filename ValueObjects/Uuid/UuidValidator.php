<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects\Uuid;

interface UuidValidator
{
    public function isValid(string $uuid): bool;
}
