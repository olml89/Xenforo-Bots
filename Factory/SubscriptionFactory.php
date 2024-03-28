<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Factory;

use olml89\XenforoSubscriptions\Exception\SubscriptionCreationException;
use olml89\XenforoSubscriptions\Exception\InvalidUrlException;
use olml89\XenforoSubscriptions\Exception\InvalidUuidException;
use olml89\XenforoSubscriptions\Exception\XFUserNotFoundException;
use olml89\XenforoSubscriptions\Service\ErrorHandler;
use olml89\XenforoSubscriptions\Service\UuidGenerator;
use olml89\XenforoSubscriptions\Service\XFUserFinder;
use XF\Mvc\Entity\Manager;

final class SubscriptionFactory
{
    public function __construct(
        private readonly XFUserFinder $xFUserFinder,
        private readonly Manager $entityManager,
        private readonly UuidGenerator $uuidGenerator,
        private readonly ErrorHandler $errorHandler,
    ) {}

    /**
     * @throws SubscriptionCreationException
     */
    public function create(int $user_id, string $password, string $webhook): Subscription
    {
        try {
            $this->xFUserFinder->findWithPassword($user_id, $password);

            /** @var Subscription $subscription */
            $subscription = $this->entityManager->create(
                shortName: 'olml89\XenforoSubscriptions:Subscription'
            );

            $subscription->subscription_id = $this->uuidGenerator->random();
            $subscription->user_id = $user_id;
            $subscription->webhook = $webhook;

            return $subscription;
        }
        catch (XFUserNotFoundException|InvalidUuidException|InvalidUrlException $e) {
            throw new SubscriptionCreationException($e, $this->errorHandler);
        }
    }
}
