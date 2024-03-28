<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Service;

use olml89\XenforoSubscriptions\XF\Mvc\Reply\ForbiddenException;
use XF;

final class Authenticator
{
    /**
     * @throws ForbiddenException
     */
    public function assertSuperUserKey(): void
    {
        if (XF::apiKey()->key_type !== 'super') {
            throw new ForbiddenException(XF::phrase('do_not_have_permission'));
        }
    }
}
