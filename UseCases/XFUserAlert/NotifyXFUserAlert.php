<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\XFUserAlert;

use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Services\WebhookNotifier\WebhookNotifier;
use XF\Entity\UserAlert as XFUserAlert;

final class NotifyXFUserAlert
{
    private const USER_ALERTS_ENDPOINT = '/user-alerts';

    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(XFUserAlert $xFUserAlert): void
    {
        return;

        $this->webhookNotifier->notify(
            endpoint: self::USER_ALERTS_ENDPOINT,
            subscriptions: $this->subscriptionRepository->getByUser($xFUserAlert->Receiver),
            data: new XFUserAlertData($xFUserAlert),
        );
    }
}
