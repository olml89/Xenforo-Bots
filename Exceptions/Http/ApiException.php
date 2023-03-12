<?php declare(strict_types=1);

namespace olml89\Subscriptions\Exceptions\Http;

use Throwable;
use XF\Api\ErrorMessage;
use XF\Mvc\Reply\Error;
use XF\Mvc\Reply\Exception as XFReplyException;

abstract class ApiException extends XFReplyException
{
    public function __construct(string $message, string $errorCode, ?Throwable $context = null)
    {
        $apiErrorMessage = new ErrorMessage(
            message: $message,
            code: $errorCode,
        );
        $apiError = new Error(
            errors: $apiErrorMessage,
            responseCode: $this->httpCode(),
        );

        if (!is_null($context)) {
            $apiError->setJsonParam('exception', $context);
        }

        parent::__construct($apiError);
    }

    abstract protected function httpCode(): int;
}
