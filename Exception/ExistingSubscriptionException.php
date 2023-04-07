<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

use olml89\XenforoSubscriptions\Entity\Subscription;
use olml89\XenforoSubscriptions\XF\Mvc\Reply\ConflictException;

final class ExistingSubscriptionException extends ConflictException
{
    public function __construct(Subscription $subscription)
    {
        parent::__construct(
            message: sprintf(
                'A suscription for the user <%s> already exists on the webhook \'%s\' with the id <%s>',
                $subscription->User->user_id,
                $subscription->webhook,
                $subscription->subscription_id,
            ),
            errorCode: 'subscription.store.error.already_exists',
        );
    }
}
