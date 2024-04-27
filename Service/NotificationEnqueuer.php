<?php declare(strict_types=1);

namespace olml89\XenforoBots\Service;

use olml89\XenforoBots\Job\NotifyContentJob;
use olml89\XenforoBots\UseCase\Notification\Content\ContentFactory;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use olml89\XenforoBots\UseCase\Notification\Notifiable;
use olml89\XenforoBots\UseCase\Notification\PublicInteraction\PublicInteractionFactory;
use olml89\XenforoBots\UseCase\Notification\PublicInteraction\UnnotifiableUserAlertException;
use olml89\XenforoBots\UseCase\Notification\PrivateInteraction\PrivateInteraction;
use olml89\XenforoBots\UseCase\Notification\PublicInteraction\PublicInteraction;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use olml89\XenforoBots\XF\Entity\UserAlert;
use XF\Entity\Post;
use XF\Job\Manager;

final class NotificationEnqueuer
{
    public function __construct(
        private readonly Manager $jobManager,
        private readonly ContentFactory $contentFactory,
        private readonly PublicInteractionFactory $publicInteractionFactory,
    ) {}

    public function enqueue(ConversationMessage|UserAlert|Post $entity): void
    {
        try {
            $notifiable = $this->createNotifiable($entity);
        }
        catch (UnnotifiableUserAlertException|NoActiveBotSubscriptionsException) {
            return;
        }

        if ($notifiable instanceof PublicInteraction) {
            /**
             * Cancel the current simultaneous NotifyContentJob for the Post matched by the PublicInteraction Post
             */
            $simultaneousNotifyContentJob = $this->jobManager->getUniqueJob(
                NotifyContentJob::getUniqueId($notifiable->entity())
            );

            if (is_array($simultaneousNotifyContentJob)) {
                $this->jobManager->cancelJob($simultaneousNotifyContentJob);

                /**
                 * Enqueue a NotifyContentJob for the Bots excluding the Bot from the PublicInteraction
                 */
                $this->enqueueNotifyContentJobFromPublicInteraction($notifiable);
            }
        }

        $this->enqueueNotifyNotifiableJob($notifiable);
    }

    /**
     * @throws UnnotifiableUserAlertException
     * @throws NoActiveBotSubscriptionsException
     */
    private function createNotifiable(ConversationMessage|Post|UserAlert $entity): Notifiable
    {
        return match (true) {
            $entity instanceof ConversationMessage => new PrivateInteraction($entity),
            $entity instanceof UserAlert => $this->publicInteractionFactory->create($entity),
            $entity instanceof Post => $this->contentFactory->create($entity),
        };
    }

    private function enqueueNotifyContentJobFromPublicInteraction(PublicInteraction $publicInteraction): void
    {
        try {
            $content = $this->contentFactory->create(
                post: $publicInteraction->entity(),
                excludedBot: $publicInteraction->bot(),
            );

            $this->enqueueNotifyNotifiableJob($content);
        }
        catch (NoActiveBotSubscriptionsException) {}
    }

    private function enqueueNotifyNotifiableJob(Notifiable $notifiable): void
    {
        $this->jobManager->enqueueUnique(
            uniqueId: $notifiable->jobCreationData()->uniqueId,
            jobClass: $notifiable->jobCreationData()->jobClass,
            params: $notifiable->jobCreationData()->params,
        );
    }
}
