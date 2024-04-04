<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\BotSubscription;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Exception\BotNotAuthorizedException;
use olml89\XenforoBots\Exception\BotSubscriptionAlreadyExistsException;
use olml89\XenforoBots\Exception\BotSubscriptionNotFoundException;
use olml89\XenforoBots\Exception\BotSubscriptionStorageException;
use olml89\XenforoBots\Exception\BotSubscriptionValidationException;
use olml89\XenforoBots\Factory\BotSubscriptionFactory;
use olml89\XenforoBots\Repository\BotSubscriptionRepository;
use olml89\XenforoBots\Finder\BotSubscriptionFinder;

final class Update
{
    public function __construct(
        private readonly BotSubscriptionFinder $botSubscriptionFinder,
        private readonly BotSubscriptionFactory $botSubscriptionFactory,
        private readonly BotSubscriptionRepository $botSubscriptionRepository,
    ) {}

    /**
     * @throws BotSubscriptionNotFoundException
     * @throws BotNotAuthorizedException
     * @throws BotSubscriptionValidationException
     * @throws BotSubscriptionAlreadyExistsException
     * @throws BotSubscriptionStorageException
     */
    public function update(Bot $bot, string $bot_subscription_id, ?string $platform_api_key, ?string $webhook): BotSubscription
    {
        $botSubscription = $this->botSubscriptionFinder->find($bot_subscription_id);
        $bot->owns($botSubscription);
        $this->botSubscriptionFactory->update($botSubscription, $platform_api_key, $webhook);
        $bot->subscribe($botSubscription);
        $this->botSubscriptionRepository->save($botSubscription);

        return $botSubscription;
    }
}
