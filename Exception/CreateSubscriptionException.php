<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

use olml89\XenforoSubscriptions\Service\ErrorHandler;
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
