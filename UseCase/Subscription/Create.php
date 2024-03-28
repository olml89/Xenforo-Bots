<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Subscription;

use olml89\XenforoBots\Entity\Subscription;
use olml89\XenforoBots\Entity\SubscriptionFactory;
use olml89\XenforoBots\Exception\SubscriptionCreationException;
use olml89\XenforoBots\Exception\ExistingSubscriptionException;
use olml89\XenforoBots\Exception\SubscriptionStorageException;
use olml89\XenforoBots\Repository\SubscriptionRepository;
use olml89\XenforoBots\XF\Api\Result\UseCaseResponse;

final class Create
{
    public function __construct(
        private readonly SubscriptionFactory $subscriptionFactory,
        private readonly SubscriptionRepository $subscriptionRepository,
    ) {}

    /**
     * @throws SubscriptionCreationException | ExistingSubscriptionException | SubscriptionStorageException
     */
    public function create(int $user_id, string $password, string $webhook): Subscription
    {
        $subscription = $this->subscriptionFactory->create($user_id, $password, $webhook);
        $this->subscriptionRepository->save($subscription);

        return $subscription;
    }
}
