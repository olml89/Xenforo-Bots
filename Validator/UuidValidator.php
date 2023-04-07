<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Validator;

use Laminas\Validator\Uuid;
use olml89\XenforoSubscriptions\Exception\InvalidUuidException;

final class UuidValidator
{
    public function __construct(
        private readonly Uuid $laminasUuidValidator,
    ) {}

    public function isValid(string $uuid): bool
    {
        return $this->laminasUuidValidator->isValid($uuid);
    }

    /**
     * @throws InvalidUuidException
     */
    public function ensureIsValid(string $uuid): void
    {
        if (!$this->isValid($uuid)) {
            throw new InvalidUuidException($uuid);
        }
    }
}
