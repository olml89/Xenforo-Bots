<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\BotSubscription;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;

final class Index
{
    /**
     * @return BotSubscription[]
     */
    public function index(Bot $bot): array
    {
        return $bot->BotSubscriptions->toArray();
    }
}