<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services\XFUserFinder;

use olml89\Subscriptions\Exceptions\InputException;
use olml89\Subscriptions\ValueObjects\UserId\UserId;

final class XFUserNotFoundException extends InputException
{
    public function __construct(UserId $userId)
    {
        parent::__construct(
            message: sprintf('User with user_id <%s> does not exist', $userId->value),
            errorCode: 'subscription.create.user_not_found',
        );
    }
}
