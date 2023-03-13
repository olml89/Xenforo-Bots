<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Exceptions\ApplicationException;
use olml89\Subscriptions\Exceptions\ErrorHandler;
use olml89\Subscriptions\XF\Mvc\Reply\BadRequestException;

final class CreateSubscriptionException extends BadRequestException
{
    public function __construct(ApplicationException $applicationException, ErrorHandler $errorHandler)
    {
        $context = $errorHandler->handle($applicationException->getPrevious());

        parent::__construct(
            message: $applicationException->getMessage(),
            errorCode: 'subscription.create.'.$applicationException->getErrorCode(),
            context: $context,
        );
    }
}
