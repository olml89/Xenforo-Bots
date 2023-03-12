<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\ValueObjects\Md5Hash\InvalidMd5HashException;
use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\InvalidUrlException;
use olml89\Subscriptions\ValueObjects\Url\Url;
use olml89\Subscriptions\ValueObjects\UserId\InvalidUserIdException;
use olml89\Subscriptions\ValueObjects\UserId\UserId;
use XF\Db\Exception as XFDatabaseException;

final class CreateSubscription
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
    ) {}

    /**
     * @throws InvalidUserIdException | InvalidUrlException | InvalidMd5HashException
     * @throws SaveSubscriptionException
     */
    public function create(int $user_id, string $webhook, string $token): void
    {
        $subscription = new Subscription(
            userId: new UserId($user_id),
            webhook: new Url($webhook),
            token: new Md5Hash($token),
        );

        try {
            $this->subscriptionRepository->save($subscription);
        }
        catch (XFDatabaseException $e) {
            throw new SaveSubscriptionException(\XF::$debugMode ? $e : null);
        }
    }
}
