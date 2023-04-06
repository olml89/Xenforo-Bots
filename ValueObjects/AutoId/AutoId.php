<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\ValueObjects\AutoId;

use olml89\XenforoSubscriptions\ValueObjects\IntValueObject;

final class AutoId extends IntValueObject
{
    /**
     * @throws InvalidAutoIdException
     */
    public function __construct(int $user_id)
    {
        $this->ensureIsBiggerThan0($user_id);

        parent::__construct($user_id);
    }

    /**
     * @throws InvalidAutoIdException
     */
    private function ensureIsBiggerThan0(int $user_id): void
    {
        if ($user_id <= 0) {
            throw new InvalidAutoIdException($user_id);
        }
    }
}
