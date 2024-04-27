<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification;

use olml89\XenforoBots\Service\Notifier\CachedRequests;
use olml89\XenforoBots\Service\Notifier\WebhookNotifier;
use XF\Error;

final class Notify
{
    public function __construct(
        private readonly WebhookNotifier $webhookNotifier,
        private readonly Error $error,
    ) {}

    public function notify(Notifiable $notifiable, CachedRequests $cachedRequests): CachedRequests
    {
        $this->webhookNotifier->notify($notifiable, $cachedRequests);

        if ($cachedRequests->completed() && $cachedRequests->hasFailedRequests()) {
            $this->error->logException(
                new NotificationFailedException($notifiable, $cachedRequests)
            );
        }

        return $cachedRequests;
    }
}