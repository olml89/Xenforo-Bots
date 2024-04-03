<?php declare(strict_types=1);

namespace olml89\XenforoBots\Factory;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Exception\BotSubscriptionAlreadyExistsException;
use olml89\XenforoBots\Exception\BotSubscriptionValidationException;
use olml89\XenforoBots\Service\UuidGenerator;
use XF\Mvc\Entity\Manager;

final class BotSubscriptionFactory
{
    public function __construct(
        private readonly Manager $entityManager,
        private readonly UuidGenerator $uuidGenerator,
    ) {}

    /**
     * @throws BotSubscriptionValidationException
     * @throws BotSubscriptionAlreadyExistsException
     */
    public function create(Bot $bot, string $webhook): BotSubscription
    {
        $botSubscription = $this->instantiateBotSubscription($webhook);

        if ($botSubscription->hasErrors()) {
            throw BotSubscriptionValidationException::entity($botSubscription);
        }

        $botSubscription->setSubscriber($bot);

        return $botSubscription;
    }

    private function instantiateBotSubscription(string $webhook): BotSubscription
    {
        /** @var BotSubscription $botSubscription */
        $botSubscription = $this->entityManager->create(
            shortName: 'olml89\XenforoBots:BotSubscription'
        );

        $botSubscription->bot_subscription_id = $this->uuidGenerator->random();
        $botSubscription->webhook = $webhook;
        $botSubscription->activate();

        return $botSubscription;
    }
}
