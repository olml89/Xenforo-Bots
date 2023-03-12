<?php declare(strict_types=1);

namespace olml89\Subscriptions\Exceptions;

use Exception;
use XF\Api\ErrorMessage;
use XF\Mvc\Reply\Error;
use XF\Mvc\Reply\Exception as XFReplyException;

abstract class ApiException extends XFReplyException
{
    public function __construct(string $message, string $errorCode, int $httpCode, ?Exception $context = null)
    {
        $apiErrorMessage = new ErrorMessage(
            message: $message,
            code: $errorCode,
            params: !is_null($context) ? ['context' => $context] : null,
        );
        $apiError = new Error(
            errors: $apiErrorMessage,
            responseCode: $httpCode,
        );

        parent::__construct($apiError);
    }
}
