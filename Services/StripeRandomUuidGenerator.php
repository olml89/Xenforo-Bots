<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services;

use olml89\Subscriptions\ValueObjects\Uuid\UuidGenerator as UuidGeneratorContract;
use Stripe\Util\RandomGenerator;

final class StripeRandomUuidGenerator implements UuidGeneratorContract
{
    public function __construct(
        private readonly RandomGenerator $generator,
    ) {}


    public function uuid(): string
    {
        return $this->generator->uuid();
    }
}
