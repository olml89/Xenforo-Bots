<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\UserId;

use olml89\Subscriptions\Exceptions\ApplicationException;

final class InvalidAutoIdException extends ApplicationException
{
    public function __construct(int $user_id)
    {
        parent::__construct(
            message: sprintf('AutoId must be bigger than 0, <%s> provided', $user_id),
            errorCode: 'invalid_auto_id',
        );
    }
}
