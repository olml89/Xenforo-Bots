<?php declare(strict_types=1);

namespace olml89\Subscriptions\Services\XFUserFinder;

use olml89\Subscriptions\Exceptions\ApplicationException;
use olml89\Subscriptions\ValueObjects\AutoId\AutoId;

final class XFUserNotFoundException extends ApplicationException
{
    public function __construct(AutoId $userId)
    {
        parent::__construct(
            message: sprintf('User with user_id <%s> does not exist', $userId->value),
            errorCode: 'user_not_found',
        );
    }
}
