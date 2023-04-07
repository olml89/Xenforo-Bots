<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCase\Subscription;

use olml89\XenforoSubscriptions\Entity\Subscription;
use olml89\XenforoSubscriptions\Entity\SubscriptionFactory;
use olml89\XenforoSubscriptions\Exception\CreateSubscriptionException;
use olml89\XenforoSubscriptions\Exception\ExistingSubscriptionException;
use olml89\XenforoSubscriptions\Exception\SaveSubscriptionException;
use olml89\XenforoSubscriptions\Repository\SubscriptionRepository;
use olml89\XenforoSubscriptions\XF\Api\Result\UseCaseResponse;

final class Create
{
    public function __construct(
        private readonly SubscriptionFactory $subscriptionFactory,
        private readonly SubscriptionRepository $subscriptionRepository,
    ) {}

    /**
     * @throws CreateSubscriptionException | ExistingSubscriptionException | SaveSubscriptionException
     */
    public function create(int $user_id, string $password, string $webhook): Subscription
    {
        $subscription = $this->subscriptionFactory->create($user_id, $password, $webhook);
        $this->subscriptionRepository->save($subscription);

        return $subscription;
    }
}
