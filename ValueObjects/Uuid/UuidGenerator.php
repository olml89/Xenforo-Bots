<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects\Uuid;

interface UuidGenerator
{
    public function create(string $uuid): Uuid;
    public function random(): Uuid;
}
