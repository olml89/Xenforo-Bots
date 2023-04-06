<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCases\Subscription;

use olml89\XenforoSubscriptions\Exceptions\ApplicationException;
use olml89\XenforoSubscriptions\Exceptions\ErrorHandler;
use olml89\XenforoSubscriptions\XF\Mvc\Reply\BadRequestException;

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
