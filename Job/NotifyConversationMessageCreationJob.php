<?php declare(strict_types=1);

namespace olml89\XenforoBots\Job;

use olml89\XenforoBots\Exception\ConversationMessageNotFoundException;
use olml89\XenforoBots\Finder\ConversationMessageFinder;
use olml89\XenforoBots\UseCase\ConversationMessage\Notify;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use XF\App;

final class NotifyConversationMessageCreationJob extends NotifyEntityCreationJob
{
    private readonly ConversationMessageFinder $conversationMessageFinder;
    private readonly Notify $notifyConversationMessage;

    public function __construct(App $app, int|string $jobId, array $data = [])
    {
        $this->conversationMessageFinder = $app->get(ConversationMessageFinder::class);
        $this->notifyConversationMessage = $app->get(Notify::class);

        parent::__construct($app, $jobId, $data);
    }

    /**
     * @param float|int $maxRunTime
     *
     * @throws ConversationMessageNotFoundException
     */
    public function run($maxRunTime): void
    {
        $conversationMessage = $this->conversationMessageFinder->find($this->getEntityId());

        $this->notifyConversationMessage->notify($conversationMessage);
    }

    protected static function entityClass(): string
    {
        return ConversationMessage::class;
    }
}
