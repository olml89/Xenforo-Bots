<?php declare(strict_types=1);

namespace olml89\Subscriptions\Exceptions;

use XF\Api\ErrorMessage;
use XF\Mvc\Reply\Error;
use XF\Mvc\Reply\Exception;

abstract class ApiException extends Exception
{
    public function __construct(string $message, int $httpCode)
    {
        $apiErrorMessage = new ErrorMessage($message, $httpCode);

        parent::__construct(new Error($apiErrorMessage));
    }
}
