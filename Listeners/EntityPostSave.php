<?php declare(strict_types=1);

namespace olml89\XenforoBots\Listeners;

use olml89\XenforoBots\UseCase\ConversationMessage\Notify as NotifyConversationMessage;
use olml89\XenforoBots\UseCase\Post\Notify as NotifyPost;
use olml89\XenforoBots\UseCase\UserAlert\Notify as NotifyUserAlert;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use olml89\XenforoBots\XF\Entity\UserAlert;
use XF;
use XF\Entity\Post;
use XF\Mvc\Entity\Entity;

final class EntityPostSave
{
    public static function listen(Entity $entity): void
    {
        if ($entity instanceof UserAlert) {
            self::instantiateUseCase(NotifyUserAlert::class)->notify($entity);

            return;
        }

        if ($entity instanceof ConversationMessage) {
            self::instantiateUseCase(NotifyConversationMessage::class)->notify($entity);

            return;
        }

        if ($entity instanceof Post) {
            self::instantiateUseCase(NotifyPost::class)->notify($entity);
        }
    }

    /**
     * @template T
     * @param class-string<T> $useCaseClass
     * @return T
     */
    private static function instantiateUseCase(string $useCaseClass): object
    {
        return XF::app()->get($useCaseClass);
    }
}
