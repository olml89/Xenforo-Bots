<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services;

use Laminas\Validator\Uuid;
use olml89\Subscriptions\ValueObjects\Uuid\UuidValidator as UuidValidatorContract;

final class LaminasUuidValidator implements UuidValidatorContract
{
    public function __construct(
        private readonly Uuid $validator,
    ) {}

    public function isValid(string $uuid): bool
    {
        return $this->validator->isValid($uuid);
    }
}
