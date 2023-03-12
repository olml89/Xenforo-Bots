<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use Exception;
use olml89\Subscriptions\Exceptions\ApiException;

final class SaveSubscriptionException extends ApiException
{
    public function __construct(?Exception $context = null)
    {
        parent::__construct(
            message: 'The subscription has failed',
            errorCode: 'subscription.store.error',
            httpCode: 500,
            context: $context,
        );
    }
}
