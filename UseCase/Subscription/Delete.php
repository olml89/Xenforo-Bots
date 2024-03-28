<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Subscription;

use olml89\XenforoBots\Exception\SubscriptionNotFoundException;
use olml89\XenforoBots\Exception\SubscriptionStorageException;
use olml89\XenforoBots\Exception\XFUserNotFoundException;
use olml89\XenforoBots\Repository\SubscriptionRepository;
use olml89\XenforoBots\Service\SubscriptionFinder;
use olml89\XenforoBots\Service\XFUserFinder;

final class Delete
{
    public function __construct(
        private readonly XFUserFinder $xFUserFinder,
        private readonly SubscriptionFinder $subscriptionFinder,
        private readonly SubscriptionRepository $subscriptionRepository,
    ) {}

    /**
     * @throws XFUserNotFoundException | SubscriptionNotFoundException | SubscriptionStorageException
     */
    public function delete(int $user_id, string $webhook): void
    {
        $xFUser = $this->xFUserFinder->find($user_id);
        $subscription = $this->subscriptionFinder->findByXFUserAndWebhook($xFUser, $webhook);
        $this->subscriptionRepository->remove($subscription);
    }
}
