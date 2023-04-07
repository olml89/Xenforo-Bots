<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Service;

use Stripe\Util\RandomGenerator;

final class UuidGenerator
{
    public function __construct(
        private readonly RandomGenerator $stripeRandomGenerator,
    ) {}

    public function random(): string
    {
        return $this->stripeRandomGenerator->uuid();
    }
}
