<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\UserAlert;

use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\UserAlert;

final class Notify
{
    private const USER_ALERTS_ENDPOINT = '/user-alerts';

    public function __construct(
        private readonly BotRepository $botRepository,
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(UserAlert $userAlert): void
    {
        $botSubscriptions = $this
            ->botRepository
            ->getByUser($userAlert->Receiver)
            ?->BotSubscriptions
            ->toArray() ?? [];

        $this->webhookNotifier->notify(
            endpoint: self::USER_ALERTS_ENDPOINT,
            data: new UserAlertData($userAlert),
            botSubscriptions: $botSubscriptions,
        );
    }
}
