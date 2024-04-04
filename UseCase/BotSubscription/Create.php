<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\BotSubscription;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Exception\BotSubscriptionAlreadyExistsException;
use olml89\XenforoBots\Exception\BotSubscriptionValidationException;
use olml89\XenforoBots\Exception\BotSubscriptionStorageException;
use olml89\XenforoBots\Factory\BotSubscriptionFactory;
use olml89\XenforoBots\Repository\BotSubscriptionRepository;

final class Create
{
    public function __construct(
        private readonly BotSubscriptionFactory $botSubscriptionFactory,
        private readonly BotSubscriptionRepository $botSubscriptionRepository,
    ) {}

    /**
     * @throws BotSubscriptionValidationException
     * @throws BotSubscriptionAlreadyExistsException
     * @throws BotSubscriptionStorageException
     */
    public function create(Bot $bot, string $platform_api_key, string $webhook): BotSubscription
    {
        $botSubscription = $this->botSubscriptionFactory->create($platform_api_key, $webhook);
        $bot->subscribe($botSubscription);
        $this->botSubscriptionRepository->save($botSubscription);

        return $botSubscription;
    }
}
