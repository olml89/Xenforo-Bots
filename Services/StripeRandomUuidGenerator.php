<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services;

use olml89\Subscriptions\ValueObjects\Uuid\Uuid;
use olml89\Subscriptions\ValueObjects\Uuid\UuidGenerator;
use olml89\Subscriptions\ValueObjects\Uuid\UuidValidator;
use Stripe\Util\RandomGenerator;

final class StripeRandomUuidGenerator implements UuidGenerator
{
    public function __construct(
        private readonly RandomGenerator $generator,
        private readonly UuidValidator $validator,
    ) {}

    public function create(string $uuid): Uuid
    {
        return new Uuid($uuid, $this->validator);
    }

    public function random(): Uuid
    {
        return $this->create($this->generator->uuid());
    }
}
