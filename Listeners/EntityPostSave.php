<?php declare(strict_types=1);

namespace olml89\XenforoBots\Listeners;

use olml89\XenforoBots\Service\NotificationEnqueuer;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use olml89\XenforoBots\XF\Entity\UserAlert;
use XF;
use XF\Entity\Post;
use XF\Mvc\Entity\Entity;

final class EntityPostSave
{
    public static function listen(Entity $entity): void
    {
        if (!($entity instanceof UserAlert || $entity instanceof ConversationMessage || $entity instanceof Post)) {
            return;
        }

        self::getNotificationEnqueuer()->enqueue($entity);
    }

    private static function getNotificationEnqueuer(): NotificationEnqueuer
    {
        return XF::app()->get(NotificationEnqueuer::class);
    }
}
