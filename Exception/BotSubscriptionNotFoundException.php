<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\BotSubscription;

final class BotSubscriptionNotFoundException extends EntityNotFoundException
{
    protected static function errorCode(): string
    {
        return 'botSubscription.retrieval.not_found';
    }

    protected static function entityClass(): string
    {
        return BotSubscription::class;
    }
}
