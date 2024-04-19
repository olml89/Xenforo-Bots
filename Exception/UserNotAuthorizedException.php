<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\XF\Entity\User;
use olml89\XenforoBots\XF\Mvc\Reply\ForbiddenException;

final class UserNotAuthorizedException extends ForbiddenException
{
    public static function noBot(User $user): self
    {
        return self::fromMessageAndErrorCode(
            message: 'User is not a Bot',
            errorCode: 'user.unauthorized',
            params: [
                'user_id' => $user->user_id,
            ]
        );
    }
}
