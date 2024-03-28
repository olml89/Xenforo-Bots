<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\XF\Entity\User as XFUser;
use olml89\XenforoBots\XF\Mvc\Reply\NotFoundException;

final class SubscriptionNotFoundException extends NotFoundException
{
    private function __construct(string $message)
    {
        parent::__construct(
            message: $message,
            errorCode: 'subscription_not_found',
        );
    }

    public static function forUserAndWebhook(XFUser $xFUser, string $webhook): self
    {
        return new self(
            sprintf(
                'Subscription for user <%s> does not exist in \'%s\'',
                $xFUser->user_id,
                $webhook,
            )
        );
    }
}
