<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCases\Subscription;

use olml89\XenforoSubscriptions\Entities\Subscription;
use olml89\XenforoSubscriptions\XF\Mvc\Reply\ConflictException;

final class ExistingSubscriptionException extends ConflictException
{
    public function __construct(Subscription $subscription)
    {
        parent::__construct(
            message: sprintf(
                'A suscription for the user <%s> already exists on the webhook \'%s\' with the id <%s>',
                $subscription->userId->toInt(),
                $subscription->webhook,
                $subscription->id,
            ),
            errorCode: 'subscription.store.error.already_exists',
        );
    }
}
