<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\BotSubscription;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotSubscriptionNotFoundException;
use olml89\XenforoBots\Exception\BotSubscriptionRemovalException;
use olml89\XenforoBots\Repository\BotSubscriptionRepository;
use olml89\XenforoBots\Service\BotSubscriptionFinder;

final class Delete
{
    public function __construct(
        private readonly BotSubscriptionFinder $botSubscriptionFinder,
        private readonly BotSubscriptionRepository $botSubscriptionRepository,
    ) {}

    /**
     * @throws BotSubscriptionNotFoundException
     * @throws BotNotAuthorizedException
     * @throws BotSubscriptionRemovalException
     */
    public function delete(Bot $bot, string $bot_subscription_id): void
    {
        $botSubscription = $this->botSubscriptionFinder->find($bot_subscription_id);
        $bot->owns($botSubscription);
        $this->botSubscriptionRepository->delete($botSubscription);
        $bot->unsubscribe($botSubscription);
    }
}
