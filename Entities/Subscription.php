<?php declare(strict_types=1);

namespace olml89\Subscriptions\Entities;

final class Subscription
{
    public function __construct(
        public readonly int $userId,
        public readonly string $webhook,
        public readonly string $token,
    ) {}
}
