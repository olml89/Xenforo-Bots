<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Exception\BotSubscriptionNotFoundException;
use olml89\XenforoBots\Repository\BotSubscriptionRepository;

final class BotSubscriptionFinder
{
    public function __construct(
        private readonly BotSubscriptionRepository $botSubscriptionRepository,
    ) {}

    /**
     * @throws BotSubscriptionNotFoundException
     */
    public function find(string $bot_subscription_id): BotSubscription
    {
        return $this
            ->botSubscriptionRepository
            ->get($bot_subscription_id) ?? throw BotSubscriptionNotFoundException::id($bot_subscription_id);
    }
}
