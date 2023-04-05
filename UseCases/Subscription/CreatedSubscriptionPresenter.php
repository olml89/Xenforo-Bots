<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\UseCases\DataTransferObject;

final class CreatedSubscriptionPresenter extends DataTransferObject
{
    public readonly string $id;
    public readonly int $user_id;
    public readonly string $webhook;
    public readonly int $subscribed_at;

    public function __construct(Subscription $subscription)
    {
        $this->id = (string)$subscription->id;
        $this->user_id = $subscription->userId->toInt();
        $this->webhook = (string)$subscription->webhook;
        $this->subscribed_at = $subscription->subscribedAt->getTimestamp();
    }
}
