<?php declare(strict_types=1);

namespace olml89\XenforoBots\Repository;

use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Exception\BotSubscriptionRemovalException;
use olml89\XenforoBots\Exception\BotSubscriptionStorageException;
use olml89\XenforoBots\Service\ErrorHandler;
use Throwable;
use XF\Mvc\Entity\Finder;

final class BotSubscriptionRepository
{
    public function __construct(
        private readonly ErrorHandler $errorHandler,
        private readonly Finder $botSubscriptionFinder,
    ) {}

    /**
     * @return BotSubscription[]
     */
    public function getByWebhook(): array
    {
        return $this
            ->botSubscriptionFinder
            ->fetch()
            ->groupBy('webhook');
    }

    public function get(string $bot_subscription_id): ?BotSubscription
    {
        /** @var BotSubscription $botSubscription */
        $botSubscription = $this
            ->botSubscriptionFinder
            ->where('bot_subscription_id', '=', $bot_subscription_id)
            ->fetchOne();

        return $botSubscription;
    }

    /**
     * @throws BotSubscriptionStorageException
     */
    public function save(BotSubscription $botSubscription): void
    {
        try {
            $botSubscription->save();
        }
        catch (Throwable $e) {
            throw BotSubscriptionStorageException::entity(
                entity: $botSubscription,
                context: $this->errorHandler->handle($e),
            );
        }
    }

    /**
     * @throws BotSubscriptionRemovalException
     */
    public function delete(BotSubscription $botSubscription): void
    {
        try {
            $botSubscription->delete();
        }
        catch (Throwable $e) {
            throw BotSubscriptionRemovalException::entity(
                entity: $botSubscription,
                context: $this->errorHandler->handle($e),
            );
        }
    }
}
