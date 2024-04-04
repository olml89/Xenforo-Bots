<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use olml89\XenforoBots\Job\NotifyConversationMessageCreationJob;
use olml89\XenforoBots\Job\NotifyEntityCreationJob;
use olml89\XenforoBots\Job\NotifyPostCreationJob;
use olml89\XenforoBots\Job\NotifyUserAlertCreationJob;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use olml89\XenforoBots\XF\Entity\UserAlert;
use XF\Entity\Post;
use XF\Job\Manager;
use XF\Mvc\Entity\Entity;

final class NotificationEnqueuer
{
    public function __construct(
        private readonly Manager $jobManager,
    ) {}

    public function enqueue(Entity $entity): void
    {
        $jobClass = $this->getJobClass($entity);

        $this->jobManager->enqueueUnique(
            uniqueId: sprintf('%s_%s', $jobClass, uniqid()),
            jobClass: $jobClass,
            params: [
                'id' => $entity->getEntityId(),
            ],
        );
    }

    /**
     * @return class-string<NotifyEntityCreationJob>
     */
    private function getJobClass(Entity $entity): string
    {
        return match (true) {
            $entity instanceof Post => NotifyPostCreationJob::class,
            $entity instanceof ConversationMessage => NotifyConversationMessageCreationJob::class,
            $entity instanceof UserAlert => NotifyUserAlertCreationJob::class,
        };
    }
}
