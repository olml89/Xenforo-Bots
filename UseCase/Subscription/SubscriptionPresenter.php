<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCase\Subscription;

use olml89\XenforoSubscriptions\Entity\Subscription;
use olml89\XenforoSubscriptions\UseCase\JsonSerializableObject;

final class SubscriptionPresenter extends JsonSerializableObject
{
    public readonly string $id;
    public readonly int $user_id;
    public readonly string $webhook;
    public readonly int $subscribed_at;

    public function __construct(Subscription $subscription)
    {
        $this->id = $subscription->subscription_id;
        $this->user_id = $subscription->User->user_id;
        $this->webhook = $subscription->webhook;
        $this->subscribed_at = $subscription->subscribed_at;
    }
}
