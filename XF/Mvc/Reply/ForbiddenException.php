<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Mvc\Reply;

use XF\Api\ErrorMessage;
use XF\Phrase;

final class ForbiddenException extends ApiException
{
    public function __construct(Phrase $phrase)
    {
        $apiErrorMessage = new ErrorMessage(
            message: $phrase->render(),
            code: $phrase->getName(),
        );

        parent::__construct($apiErrorMessage);
    }

    protected static function httpCode(): int
    {
        return 403;
    }
}
