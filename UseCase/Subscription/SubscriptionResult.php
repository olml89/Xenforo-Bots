<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCase\Subscription;

use olml89\XenforoSubscriptions\UseCase\JsonSerializableObject;

final class SubscriptionResult extends JsonSerializableObject
{
    public readonly bool $success;

    public function __construct(
        public readonly SubscriptionPresenter $subscription,
    ) {
        $this->success = true;
    }
}
