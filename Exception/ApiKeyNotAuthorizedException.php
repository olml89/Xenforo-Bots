<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\XF\Entity\ApiKey;
use olml89\XenforoBots\XF\Mvc\Reply\ForbiddenException;

final class ApiKeyNotAuthorizedException extends ForbiddenException
{
    public static function superUserRequired(ApiKey $apiKey): self
    {
        return self::fromMessageAndErrorCode(
            message: 'ApiKey has not super user permissions, super user permissions are required',
            errorCode: 'api_key.unauthorized.super_user_required',
            params: [
                'api_key_id' => $apiKey->api_key_id,
                'api_key' => $apiKey->api_key,
            ]
        );
    }

    public static function superUserNotAllowed(ApiKey $apiKey): self
    {
        return self::fromMessageAndErrorCode(
            message: 'ApiKey has super user permissions, user permissions are required',
            errorCode: 'api_key.unauthorized.super_user_not_allowed',
            params: [
                'api_key_id' => $apiKey->api_key_id,
                'api_key' => $apiKey->api_key,
            ]
        );
    }

    public static function doesNotOwn(ApiKey $apiKey, Bot $bot): self
    {
        return self::fromMessageAndErrorCode(
            message: 'Bot is not owned by this ApiKey',
            errorCode: 'api_key.unauthorized',
            params: [
                'api_key_id' => $apiKey->api_key_id,
                'bot_id' => $bot->bot_id,
            ]
        );
    }
}