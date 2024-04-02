<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Mvc\Reply;

use XF\Phrase;

class ForbiddenException extends ApiException
{
    protected static function httpCode(): int
    {
        return 403;
    }

    public static function phrase(Phrase $phrase): self
    {
        return self::fromMessageAndErrorCode(
            message: $phrase->render(),
            errorCode: $phrase->getName(),
        );
    }
}
