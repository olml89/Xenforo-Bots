<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\UserAlert;

use olml89\XenforoBots\Entity\ActiveBotSubscriptionCollection;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\UserAlert;

final class Notify
{
    private const USER_ALERTS_ENDPOINT = '/user-alerts';

    public function __construct(
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(UserAlert $userAlert): void
    {
        $notifiableBot = $userAlert->Receiver->Bot;

        if (is_null($notifiableBot)) {
            return;
        }

        $activeBotSubscriptions = new ActiveBotSubscriptionCollection($notifiableBot);

        $this->webhookNotifier->notify(
            endpoint: self::USER_ALERTS_ENDPOINT,
            data: new UserAlertData($userAlert),
            botSubscriptions: $activeBotSubscriptions,
        );
    }
}
