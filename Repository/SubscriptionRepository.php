<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Repository;

use Exception;
use olml89\XenforoSubscriptions\Entity\Subscription;
use olml89\XenforoSubscriptions\Exception\ExistingSubscriptionException;
use olml89\XenforoSubscriptions\Exception\SubscriptionStorageException;
use olml89\XenforoSubscriptions\Service\ErrorHandler;
use olml89\XenforoSubscriptions\XF\Entity\User as XFUser;
use XF\Db\DuplicateKeyException;
use XF\Mvc\Entity\Finder;
use XF\PrintableException;

final class SubscriptionRepository
{
    public function __construct(
        private readonly Finder $subscriptionFinder,
        private readonly ErrorHandler $errorHandler,
    ) {}

    /**
     * @return Subscription[]
     */
    public function getByUser(XFUser $xFUser): array
    {
        return $this->subscriptionFinder
            ->where('user_id', '=', $xFUser->user_id)
            ->fetchOne();
    }

    /**
     * @param XFUser[] $xFUsers
     *
     * @return Subscription[]
     */
    public function getByUsers(array $xFUsers): array
    {
        // https://xenforo.com/community/threads/add-in-and-not-in-operators-for-the-finder.140672/#post-1210589
        return $this->subscriptionFinder
            ->where(
                'user_id',
                '=',
                array_map(
                    fn(XFUser $xFUser): int => $xFUser->user_id,
                    $xFUsers
                )
            )
            ->fetch()
            ->toArray();
    }

    /**
     * @return Subscription[]
     */
    public function groupByWebhook(): array
    {
        /**
         * The Xenforo 2 Finder does not allow to do native GROUP BY queries, it groups the entities
         * after a whole fetch() processing the returned collection.
         *
         * groupBy() returns an array of $value => $entities where $value is one of the values the specified
         * grouping field has in the database and $entities all the entities that have that same value, keyed
         * by their subscription_id.
         *
         * @var array<string, array<string, Subscription>> $subscriptionsGroupedByWebhook
         */
        $subscriptionsGroupedByWebhook = $this->subscriptionFinder
            ->fetch()
            ->groupBy('webhook');

        /**
         * This is not convenient for two reasons, one being that is not standard, and while it serves better
         * our purposes (because what we want in the WebhookNotifier is a list of webhooks, not Subscriptions,
         * this repository should return groups of Subscriptions and the WebhookNotifier expects an array of
         * Subscriptions in all the cases.
         *
         * So we have to convert this.
         *
         * (We use array_values to override the string keys (subscription_id in $subscriptions and
         * webhook in $subscriptionsGroupedByWebhook) and work with numeric keys).
         */
        return array_values(
            array_map(
                /** @var array<string, Subscription> $subscriptions */
                fn(array $subscriptions): Subscription => array_values($subscriptions)[0],
                $subscriptionsGroupedByWebhook
            )
        );
    }

    /**
     * @throws ExistingSubscriptionException | SubscriptionStorageException
     */
    public function save(Subscription $subscription): void
    {
        try {
            $subscription->save();
        }
        catch (DuplicateKeyException) {
            throw new ExistingSubscriptionException(
                subscription: $subscription
            );
        }
        catch (Exception $e) {
            throw new SubscriptionStorageException(
                exception: $e,
                errorHandler: $this->errorHandler,
            );
        }
    }

    /**
     * @throws SubscriptionStorageException
     */
    public function remove(Subscription $subscription): void
    {
        try {
            $subscription->delete();
        }
        catch (Exception $e) {
            throw new SubscriptionStorageException(
                exception: $e,
                errorHandler: $this->errorHandler,
            );
        }
    }
}
