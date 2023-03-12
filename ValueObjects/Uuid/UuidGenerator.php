<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Uuid;

interface UuidGenerator
{
    public function uuid(): string;
}
