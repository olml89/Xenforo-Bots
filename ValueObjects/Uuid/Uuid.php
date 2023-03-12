<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Uuid;

use olml89\Subscriptions\ValueObjects\StringValueObject;

final class Uuid extends StringValueObject
{

    public function __construct(string $uuid, UuidValidator $validator)
    {
        $this->ensureIsAValidUuid($uuid, $validator);

        parent::__construct($uuid);
    }

    public static function random(UuidGenerator $generator, UuidValidator $validator): self
    {
        return new self($generator->uuid(), $validator);
    }

    private function ensureIsAValidUuid(string $uuid, UuidValidator $validator): void
    {
        if (!$validator->isValid($uuid)) {
            throw new InvalidUuidException($uuid);
        }
    }
}
