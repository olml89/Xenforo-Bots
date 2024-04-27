<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification;

use Exception;
use olml89\XenforoBots\Service\Notifier\CachedRequests;

final class NotificationFailedException extends Exception
{
    public function __construct(Notifiable $notifiable, CachedRequests $cachedRequests)
    {
        parent::__construct(
            sprintf(
                'Some requests for job %s (%s %s) failed after %s attempts:' . PHP_EOL . '%s',
                $notifiable->jobCreationData()->jobClass,
                $notifiable->entity()::class,
                $notifiable->entity()->getEntityId(),
                $cachedRequests->maxAttempts(),
                implode(', ', $cachedRequests->failed())
            )
        );
    }
}