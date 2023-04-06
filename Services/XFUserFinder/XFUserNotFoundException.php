<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Services\XFUserFinder;

use olml89\XenforoSubscriptions\Exceptions\ApplicationException;
use olml89\XenforoSubscriptions\ValueObjects\AutoId\AutoId;

final class XFUserNotFoundException extends ApplicationException
{
    private function __construct(string $message)
    {
        parent::__construct(
            message: $message,
            errorCode: 'user_not_found',
        );
    }

    public static function unexisting(AutoId $userId): self
    {
        return new self(sprintf('User with user_id <%s> does not exist', $userId->toInt()));
    }

    public static function invalidPassword(): self
    {
        return new self('Invalid password');
    }
}
