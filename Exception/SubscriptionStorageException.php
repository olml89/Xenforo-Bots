<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use Exception;
use olml89\XenforoBots\Service\ErrorHandler;
use olml89\XenforoBots\XF\Mvc\Reply\InternalServerErrorException;

final class SubscriptionStorageException extends InternalServerErrorException
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
