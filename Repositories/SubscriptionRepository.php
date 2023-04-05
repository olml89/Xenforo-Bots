<?php declare(strict_types=1);

namespace olml89\Subscriptions\Repositories;

use DateTimeImmutable;
use InvalidArgumentException;
use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\Services\EntityHydrator;
use olml89\Subscriptions\ValueObjects\AutoId\AutoId;
use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\Url;
use olml89\Subscriptions\ValueObjects\Uuid\Uuid;
use ReflectionException;
use XF\Entity\User as XFUser;
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
        try {
            $subscription = EntityHydrator::getInstance(Subscription::class);
            $hydrator = new EntityHydrator($subscription);

            $hydrator
                ->hydrateProperty(
                    property: 'id',
                    value: $hydrator->hydrateValueObject(Uuid::class, $row['id']),
                )
                ->hydrateProperty(
                    property: 'userId',
                    value: $hydrator->hydrateValueObject(AutoId::class, $row['user_id']),
                )
                ->hydrateProperty(
                    property: 'webhook',
                    value: $hydrator->hydrateValueObject(Url::class, $row['webhook']),
                )
                ->hydrateProperty(
                    property: 'subscribedAt',
                    value: $hydrator->hydrateDateTimeImmutable($row['subscribed_at']),
                );

            return $subscription;
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
     * @return Subscription[]
     */
    public function getByUser(XFUser $xfUser): array
    {
        $rows = $this->entityManager->getDb()->fetchAll(
            query: 'SELECT * FROM `xf_subscriptions` WHERE user_id = ?',
            params: ['user_id' => $xfUser->user_id]
        );

        return $this->createInstances($rows);
    }

    /**
     * @param XFUser[] $xFUsers
     *
     * @return Subscription[]
     */
    public function getByUsers(array $xFUsers): array
    {
        $userIds = array_map(
            function (XFUser $xFUser) : int {
                return $xFUser->user_id;
            },
            $xFUsers
        );

        $userIdsPadding = implode(
            separator: ',',
            array: array_fill(0, count($userIds), '?')
        );

        $rows = $this->entityManager->getDb()->fetchAll(
            query: 'SELECT * FROM `xf_subscriptions` WHERE user_id IN ('.$userIdsPadding.')',
            params: $userIds
        );

        return $this->createInstances($rows);
    }

    public function save(Subscription $subscription): void
    {
        $this->entityManager->getDb()->insert(
            table: 'xf_subscriptions',
            rawValues: [
                'id' => (string)$subscription->id,
                'user_id' => $subscription->userId->toInt(),
                'webhook' => (string)$subscription->webhook,
                'subscribed_at' => $subscription->subscribedAt->format('c'),
            ],
        );
    }
}
