<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects\Uuid;

use olml89\XenforoSubscriptions\ValueObjects\StringValueObject;

final class Uuid extends StringValueObject
{
    public function __construct(string $uuid, UuidValidator $validator)
    {
        $this->ensureIsAValidUuid($uuid, $validator);

        parent::__construct($uuid);
    }

    private function ensureIsAValidUuid(string $uuid, UuidValidator $validator): void
    {
        if (!$validator->isValid($uuid)) {
            throw new InvalidUuidException($uuid);
        }
    }
}
