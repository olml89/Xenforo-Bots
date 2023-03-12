<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\Exceptions\Http\ConflictException;

final class ExistingSubscriptionException extends ConflictException
{
    public function __construct(Subscription $subscription)
    {
        parent::__construct(
            message: sprintf(
                'A suscription for the user <%s> already exists on the webhook \'%s\' with the id <%s>',
                $subscription->userId->value,
                $subscription->webhook,
                $subscription->id,
            ),
            errorCode: 'subscription.store.error.already_exists',
        );
    }
}
