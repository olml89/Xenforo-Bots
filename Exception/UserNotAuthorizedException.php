<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\XF\Entity\User;
use olml89\XenforoBots\XF\Mvc\Reply\ForbiddenException;

final class UserNotAuthorizedException extends ForbiddenException
{
    public static function noSuperUser(User $user): self
    {
        return self::fromMessageAndErrorCode(
            message: sprintf(
                'User \'%s\' is not a Bot',
                $user->user_id,
            ),
            errorCode: 'invalid_user',
        );
    }

    public static function noPlainUser(User $user): self
    {
        return self::fromMessageAndErrorCode(
            message: sprintf(
                'User \'%s\' has not plain user permissions',
                $user->user_id,
            ),
            errorCode: 'invalid_user',
        );
    }

    public static function noBot(User $user): self
    {
        return self::fromMessageAndErrorCode(
            message: sprintf(
                'User \'%s\' is not a Bot',
                $user->user_id,
            ),
            errorCode: 'invalid_user',
        );
    }

    public static function unauthorizedBot(Bot $bot): self
    {
        return self::fromMessageAndErrorCode(
            message: sprintf(
                'Bot \'%s\' is not authorized to perform this action',
                $bot->bot_id,
            ),
            errorCode: 'unauthorized_bot',
        );
    }
}
