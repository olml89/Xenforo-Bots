<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Service\ErrorHandler;
use olml89\XenforoBots\XF\Mvc\Reply\UnprocessableEntityException;

final class SubscriptionCreationException extends UnprocessableEntityException
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
