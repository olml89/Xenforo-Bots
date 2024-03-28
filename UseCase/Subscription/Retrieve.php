<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Subscription;

use olml89\XenforoBots\Entity\Subscription;
use olml89\XenforoBots\Exception\SubscriptionNotFoundException;
use olml89\XenforoBots\Exception\XFUserNotFoundException;
use olml89\XenforoBots\Service\XFUserFinder;

final class Retrieve
{
    public function __construct(
        private readonly XFUserFinder $xFUserFinder,
    ) {}

    /**
     * @throws XFUserNotFoundException | SubscriptionNotFoundException
     */
    public function retrieve(int $user_id, string $webhook): Subscription
    {
        $xFUser = $this->xFUserFinder->find($user_id);
        $subscription = $xFUser->getSubscriptionByWebhook($webhook);

        if (is_null($subscription)) {
            throw SubscriptionNotFoundException::forUserAndWebhook($xFUser, $webhook);
        }

        return $subscription;
    }
}
