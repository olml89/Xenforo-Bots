<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Services;

use Laminas\Validator\Uuid;
use olml89\XenforoSubscriptions\ValueObjects\Uuid\UuidValidator as UuidValidatorContract;

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
