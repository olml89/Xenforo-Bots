<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\Md5Hash;

use olml89\Subscriptions\ValueObjects\StringValueObject;

final class Md5Hash extends StringValueObject
{
    /**
     * @throws InvalidMd5HashException
     */
    public function __construct(string $hash)
    {
        $this->ensureIsAValidMd5Hash($hash);

        parent::__construct($hash);
    }

    /**
     * @throws InvalidMd5HashException
     */
    private function ensureIsAValidMd5Hash(string $hash): void
    {
        if (strlen($hash) !== 32) {
            throw new InvalidMd5HashException($hash);
        }
    }
}
