<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\XFUserAlert;

use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Services\WebhookNotifier\WebhookNotifier;
use XF\Entity\UserAlert as XFUserAlert;

final class NotifyXFUserAlert
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(XFUserAlert $xFUserAlert): void
    {
        return;

        $this->webhookNotifier->notify(
            endpoint: 'user-alert',
            subscriptions: $this->subscriptionRepository->getByUser($xFUserAlert->Receiver),
            data: new XFUserAlertData($xFUserAlert),
        );
    }
}
