<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Services\WebhookVerifier\WebhookNotImplementedException;
use olml89\Subscriptions\Services\WebhookVerifier\WebhookVerifier;
use olml89\Subscriptions\Services\XFUserFinder\XFUserFinder;
use olml89\Subscriptions\Services\XFUserFinder\XFUserNotFoundException;
use olml89\Subscriptions\ValueObjects\Md5Hash\InvalidMd5HashException;
use olml89\Subscriptions\ValueObjects\Md5Hash\Md5Hash;
use olml89\Subscriptions\ValueObjects\Url\InvalidUrlException;
use olml89\Subscriptions\ValueObjects\Url\Url;
use olml89\Subscriptions\ValueObjects\UserId\InvalidUserIdException;
use olml89\Subscriptions\ValueObjects\UserId\UserId;
use XF\Db\Exception as XFDatabaseException;
use XF\Validator\Url as XFUrlValidator;

final class CreateSubscription
{
    public function __construct(
        private readonly XFUserFinder $xFUserFinder,
        private readonly XFUrlValidator $xFUrlValidator,
        private readonly WebhookVerifier $webhookVerifier,
        private readonly SubscriptionRepository $subscriptionRepository,
    ) {}

    /**
     * @throws InvalidUserIdException | InvalidUrlException | InvalidMd5HashException
     * @throws XFUserNotFoundException | WebhookNotImplementedException
     * @throws SaveSubscriptionException
     */
    public function create(int $user_id, string $webhook, string $token): void
    {
        $subscription = new Subscription(
            userId: new UserId($user_id),
            webhook: new Url($webhook, $this->xFUrlValidator),
            token: new Md5Hash($token),
        );

        $this->xFUserFinder->find($subscription->userId);
        $this->webhookVerifier->verify($subscription->webhook, $subscription->token);

        try {
            $this->subscriptionRepository->save($subscription);
        }
        catch (XFDatabaseException $e) {
            throw new SaveSubscriptionException(\XF::$debugMode ? $e : null);
        }
    }
}
