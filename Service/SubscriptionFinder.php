<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use olml89\XenforoBots\Entity\Subscription;
use olml89\XenforoBots\Exception\SubscriptionNotFoundException;
use olml89\XenforoBots\Validator\Url;
use olml89\XenforoBots\XF\Entity\User as XFUser;

final class SubscriptionFinder
{
    public function __construct(
        private readonly Url $urlValidator,
    ) {}

    /**
     * @throws SubscriptionNotFoundException
     */
    public function findByXFUserAndWebhook(XFUser $xFUser, string $webhook): Subscription
    {
        $this->urlValidator->ensureIsValid($webhook);

        $foundSubscription = null;

        foreach ($xFUser->Subscriptions as $subscription) {
            if (!is_null($foundSubscription)) {
                break;
            }

            if ($subscription->webhook === $webhook) {
                $foundSubscription = $subscription;
            }
        }

        return $foundSubscription
            ?? throw SubscriptionNotFoundException::forUserAndWebhook($xFUser, $webhook);
    }
}
