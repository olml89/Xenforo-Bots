<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\XFUserAlert;

use olml89\XenforoBots\Repository\SubscriptionRepository;
use olml89\XenforoBots\Service\WebhookNotifier;
use XF\Entity\UserAlert as XFUserAlert;

final class Notify
{
    private const USER_ALERTS_ENDPOINT = '/user-alerts';

    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(XFUserAlert $xFUserAlert): void
    {
        $this->webhookNotifier->notify(
            endpoint: self::USER_ALERTS_ENDPOINT,
            subscriptions: [$this->subscriptionRepository->getByUser($xFUserAlert->Receiver)],
            data: new XFUserAlertData($xFUserAlert),
        );
    }
}
