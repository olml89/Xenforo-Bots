<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Service;

use olml89\XenforoSubscriptions\Entity\Subscription;
use olml89\XenforoSubscriptions\Exception\SubscriptionNotFoundException;
use olml89\XenforoSubscriptions\Validator\UrlValidator;
use olml89\XenforoSubscriptions\XF\Entity\User as XFUser;

final class SubscriptionFinder
{
    public function __construct(
        private readonly UrlValidator $urlValidator,
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
