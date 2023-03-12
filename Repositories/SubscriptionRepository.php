<?php declare(strict_types=1);

namespace olml89\Subscriptions\Repositories;

use InvalidArgumentException;
use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\ValueObjects\AutoId\AutoId;
use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\Url;
use olml89\Subscriptions\ValueObjects\Uuid\Uuid;
use olml89\Subscriptions\ValueObjects\ValueObjectHydrator;
use ReflectionException;
use XF\Db\Exception;
use XF\Error;
use XF\Mvc\Entity\Manager;

final class SubscriptionRepository
{
    public function __construct(
        private readonly Manager $entityManager,
        private readonly Error $error,
    ) {}

    private function hydrateInstance(array $row): ?Subscription
    {
        $hydrator = new ValueObjectHydrator($row);

        try {
            return new Subscription(
                id: $hydrator->hydrateValueObject(Uuid::class, 'id'),
                userId: $hydrator->hydrateValueObject(AutoId::class, 'user_id'),
                webhook: $hydrator->hydrateValueObject(Url::class,'webhook'),
                token: $hydrator->hydrateValueObject(Md5Hash::class,'token'),
            );
        }
        catch(InvalidArgumentException|ReflectionException $e) {
            $this->error->logException($e);
            return null;
        }
    }

    /**
     * @return Subscription[]
     */
    private function createInstances(array $rows): array
    {
        return array_filter(
            array_map(
                function (array $row): ?Subscription {
                    return $this->hydrateInstance($row);
                },
                $rows,
            )
        );
    }

    /**
     * @return Subscription[]
     */
    public function getByWebhook(): array
    {
        $rows = $this->entityManager->getDb()->fetchAll(
            'SELECT * FROM `xf_subscriptions` GROUP BY `webhook`'
        );

        return $this->createInstances($rows);
    }

    /**
     * @throws Exception
     */
    public function save(Subscription $subscription): void
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
