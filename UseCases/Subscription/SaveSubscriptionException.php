<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Exceptions\ErrorHandler;
use olml89\Subscriptions\Exceptions\Http\ApiException;
use XF\Db\Exception as XFDatabaseException;

final class SaveSubscriptionException extends ApiException
{
    public function __construct(XFDatabaseException $xfDatabaseException, ErrorHandler $errorHandler)
    {
        $context = $errorHandler->handle($xfDatabaseException);

        parent::__construct(
            message: 'The subscription has failed',
            errorCode: 'subscription.store.error',
            httpCode: 500,
            context: $context,
        );
    }
}
