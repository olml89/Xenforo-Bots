<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\BotSubscription;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotSubscriptionNotFoundException;
use olml89\XenforoBots\Service\BotSubscriptionFinder;

final class Retrieve
{
    public function __construct(
        private readonly BotSubscriptionFinder $botSubscriptionFinder,
    ) {}

    /**
     * @throws BotSubscriptionNotFoundException
     * @throws BotNotAuthorizedException
     */
    public function retrieve(Bot $bot, string $bot_subscription_id): BotSubscription
    {
        $botSubscription = $this->botSubscriptionFinder->find($bot_subscription_id);
        $bot->owns($botSubscription);

        return $botSubscription;
    }
}
