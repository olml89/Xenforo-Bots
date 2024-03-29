<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Mvc\Reply;

use Throwable;
use XF\Api\ErrorMessage;
use XF\Mvc\Reply\Error;
use XF\Mvc\Reply\Exception;

abstract class ApiException extends Exception
{
    protected function __construct(ErrorMessage $apiErrorMessage, ?Throwable $context = null)
    {
        $apiError = new Error(
            errors: $apiErrorMessage,
            responseCode: static::httpCode(),
        );

        if (!is_null($context)) {
            $apiError->setJsonParam('exception', $context);
        }

        parent::__construct($apiError);
    }

    abstract protected static function httpCode(): int;

    protected static function fromMessageAndErrorCode(string $message, string $errorCode, ?Throwable $context = null): static
    {
        $apiErrorMessage = new ErrorMessage(
            message: $message,
            code: $errorCode,
        );

        return new static($apiErrorMessage, $context);
    }
}
