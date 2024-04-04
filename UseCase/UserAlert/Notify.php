<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\UserAlert;

use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\UserAlert;

final class Notify
{
    private const USER_ALERTS_ENDPOINT = '/user-alerts';
    private const NOTIFIABLE_CONTENT_TYPE = 'post';

    private const NOTIFIABLE_ACTIONS = [
        'quote',
        'mention',
    ];

    public function __construct(
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(UserAlert $userAlert): void
    {
        if (!$this->isNotifiable($userAlert)) {
            return;
        }

        $this->webhookNotifier->notify(
            endpoint: self::USER_ALERTS_ENDPOINT,
            data: new UserAlertData($userAlert),
            botSubscriptions: $userAlert->Receiver->Bot->BotSubscriptions,
        );
    }

    private function isNotifiable(UserAlert $userAlert): bool
    {
        return $userAlert->content_type === self::NOTIFIABLE_CONTENT_TYPE
            && in_array($userAlert->action, self::NOTIFIABLE_ACTIONS)
            && !is_null($userAlert->Receiver->Bot);
    }
}
