<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

use Exception;
use olml89\XenforoSubscriptions\Service\ErrorHandler;
use olml89\XenforoSubscriptions\XF\Mvc\Reply\InternalServerErrorException;

final class SaveSubscriptionException extends InternalServerErrorException
{
    public function __construct(Exception $exception, ErrorHandler $errorHandler)
    {
        $context = $errorHandler->handle($exception);

        parent::__construct(
            message: 'The subscription has failed',
            errorCode: 'subscription.store.error.database_error',
            context: $context,
        );
    }
}
