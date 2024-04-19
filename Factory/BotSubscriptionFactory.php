<?php declare(strict_types=1);

namespace olml89\XenforoBots\Factory;

use olml89\XenforoBots\Entity\BotSubscription;
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
     */
    public function create(string $platform_api_key, string $webhook): BotSubscription
    {
        $botSubscription = $this->instantiateBotSubscription();
        $this->update($botSubscription, $platform_api_key, $webhook);

        return $botSubscription;
    }

    /**
     * @throws BotSubscriptionValidationException
     */
    public function update(BotSubscription $botSubscription, ?string $platform_api_key, ?string $webhook): void
    {
        if (!is_null($platform_api_key)) {
            $botSubscription->platform_api_key = $platform_api_key;
        }

        if (!is_null($webhook)) {
            $botSubscription->webhook = $webhook;
        }

        if ($botSubscription->hasErrors()) {
            throw BotSubscriptionValidationException::entity($botSubscription);
        }
    }

    private function instantiateBotSubscription(): BotSubscription
    {
        /** @var BotSubscription $botSubscription */
        $botSubscription = $this->entityManager->create(
            shortName: 'olml89\XenforoBots:BotSubscription'
        );

        $botSubscription->bot_subscription_id = $this->uuidGenerator->random();
        $botSubscription->deactivate();

        return $botSubscription;
    }
}
