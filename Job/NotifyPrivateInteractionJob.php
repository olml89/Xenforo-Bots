<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use olml89\XenforoBots\Exception\ConversationMessageNotFoundException;
use olml89\XenforoBots\Finder\ConversationMessageFinder;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use olml89\XenforoBots\UseCase\Notification\Notify;
use olml89\XenforoBots\UseCase\Notification\PrivateInteraction\PrivateInteraction;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use XF\App;
use XF\Job\JobResult;

final class NotifyPrivateInteractionJob extends NotifyNotifiableJob
{
    private readonly ConversationMessageFinder $conversationMessageFinder;
    private readonly Notify $notifyNotifiable;

    public function __construct(App $app, int|string $jobId, array $data = [])
    {
        $this->conversationMessageFinder = $app->get(ConversationMessageFinder::class);
        $this->notifyNotifiable = $app->get(Notify::class);

        parent::__construct($app, $jobId, $data);
    }

    public static function getCreationData(ConversationMessage $conversationMessage): NotifyNotifiableJobData
    {
        return new NotifyNotifiableJobData(
            uniqueId: self::getUniqueId($conversationMessage),
            jobClass: self::class,
            params: [
                'conversation_message_id' => $conversationMessage->getEntityId(),
            ]
        );
    }

    /**
     * @param float|int $maxRunTime
     *
     * @throws ConversationMessageNotFoundException
     * @throws NoActiveBotSubscriptionsException
     */
    public function run($maxRunTime): JobResult
    {
        $privateInteraction = $this->getPrivateInteraction();

        $cachedRequests = $this->notifyNotifiable->notify(
            $privateInteraction,
            $this->getCachedRequests()
        );

        return $this->getJobResult($cachedRequests);
    }

    /**
     * @throws ConversationMessageNotFoundException
     * @throws NoActiveBotSubscriptionsException
     */
    private function getPrivateInteraction(): PrivateInteraction
    {
        return new PrivateInteraction(
            conversationMessage: $this->conversationMessageFinder->find($this->getEntityId())
        );
    }

    protected function getEntityId(): int|string
    {
        return $this->data['conversation_message_id'];
    }

    protected static function notifiableClass(): string
    {
        return PrivateInteraction::class;
    }

    protected static function entityClass(): string
    {
        return ConversationMessage::class;
    }
}
