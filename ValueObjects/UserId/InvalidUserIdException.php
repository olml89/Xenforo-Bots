<?php declare(strict_types=1);

namespace olml89\Subscriptions\ValueObjects\UserId;

use olml89\Subscriptions\Exceptions\ApiException;

final class InvalidUserIdException extends ApiException
{
    public function __construct(int $user_id)
    {
        parent::__construct(
            message: sprintf('User id must be bigger than 0, <%s> provided', $user_id),
            httpCode: 400,
        );
    }
}
