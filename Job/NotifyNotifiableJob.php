<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use olml89\XenforoBots\Service\Notifier\CachedRequests;
use olml89\XenforoBots\UseCase\Notification\Notifiable;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use olml89\XenforoBots\XF\Entity\UserAlert;
use ReflectionClass;
use ReflectionException;
use XF\Entity\Post;
use XF\Job\AbstractJob;
use XF\Job\JobResult;

abstract class NotifyNotifiableJob extends AbstractJob
{
    /**
     * @return class-string<Notifiable>
     */
    abstract protected static function notifiableClass(): string;

    /**
     * @return class-string<ConversationMessage|UserAlert|Post>
     */
    abstract protected static function entityClass(): string;

    abstract protected function getEntityId(): int|string;

    public static function getUniqueId(ConversationMessage|UserAlert|Post $entity): string
    {
        return sprintf(
            '%s_%s_%s',
            self::shortNotifiableClass() ?? static::notifiableClass(),
            self::shortEntityClass() ?? static::entityClass(),
            $entity->getEntityId(),
        );
    }

    private static function shortNotifiableClass(): ?string
    {
        try {
            return (new ReflectionClass(static::notifiableClass()))->getShortName();
        }
        catch (ReflectionException) {
            return null;
        }
    }

    private static function shortEntityClass(): ?string
    {
        try {
            return (new ReflectionClass(static::entityClass()))->getShortName();
        }
        catch (ReflectionException) {
            return null;
        }
    }

    protected function getCachedRequests(): CachedRequests
    {
        return new CachedRequests($this->data['cached_requests'] ?? []);
    }

    protected function getJobResult(CachedRequests $cachedRequests): JobResult
    {
        $this->data['cached_requests'] = $cachedRequests->toArray();

        return new JobResult(
            completed: $cachedRequests->completed(),
            jobId: $this->jobId,
            data: $this->data,
            statusMessage: $this->getStatusMessage(),
            canCancel: $this->canCancel()
        );
    }

    public function getStatusMessage(): string
    {
        return sprintf(
            'Executed %s for %s with id %s',
            self::shortNotifiableClass() ?? static::notifiableClass(),
            self::shortEntityClass() ?? static::entityClass(),
            $this->getEntityId(),
        );
    }

    public function canCancel(): bool
    {
        return true;
    }

    public function canTriggerByChoice(): bool
    {
        return false;
    }
}
