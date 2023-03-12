<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\Subscription;

use olml89\Subscriptions\Entities\Subscription;
use olml89\Subscriptions\UseCases\DataTransferObject;

final class CreatedSubscriptionPresenter extends DataTransferObject
{
    public readonly string $id;
    public readonly int $user_id;
    public readonly string $webhook;
    public readonly string $token;

    public function __construct(Subscription $subscription)
    {
        $this->id = $subscription->id->value;
        $this->user_id = $subscription->userId->value;
        $this->webhook = $subscription->webhook->value;
        $this->token = $subscription->token->value;
    }
}
