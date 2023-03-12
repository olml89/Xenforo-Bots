<?php declare(strict_types=1);

namespace olml89\Subscriptions\Repositories;

use olml89\Subscriptions\Entities\Subscription;
use XF\Db\Exception;
use XF\Mvc\Entity\Manager;

final class SubscriptionRepository
{
    public function __construct(
        private readonly Manager $entityManager,
    ) {}

    /**
     * @throws Exception
     */
    public function save(Subscription $subscription) : void
    {
        $this->entityManager->getDb()->insert(
            table: 'xf_subscriptions',
            rawValues: [
                'id' => $subscription->id,
                'user_id' => $subscription->userId->value,
                'webhook' => $subscription->webhook,
                'token' => $subscription->token,
            ],
        );
    }
}
