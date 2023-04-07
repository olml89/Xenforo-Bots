<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

use olml89\XenforoSubscriptions\XF\Mvc\Reply\NotFoundException;

final class SubscriptionNotFoundException extends NotFoundException
{
    private function __construct(string $message)
    {
        parent::__construct(
            message: $message,
            errorCode: 'subscription_not_found',
        );
    }

    public static function forUser(int $user_id): self
    {
        return new self(
            sprintf('Subscription for user <%s> does not exist', $user_id)
        );
    }
}
