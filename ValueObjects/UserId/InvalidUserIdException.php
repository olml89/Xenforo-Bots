<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\UserId;

use olml89\Subscriptions\Exceptions\InputException;

final class InvalidUserIdException extends InputException
{
    public function __construct(int $user_id)
    {
        parent::__construct(
            message: sprintf('User id must be bigger than 0, <%s> provided', $user_id),
            errorCode: 'invalid_user_id',
        );
    }
}
