<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\ConversationMessage;

use olml89\XenforoBots\Repository\BotRepository;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\User;
use XF\Entity\ConversationMessage;


final class Notify
{
    private const CONVERSATION_MESSAGES_ENDPOINT = '/conversation-messages';

    public function __construct(
        private readonly BotRepository $botRepository,
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    /**
     * @param User[] $usersNotified
     */
    public function notify(ConversationMessage $conversationMessage, array $usersNotified): void
    {
        $notifiedBots = $this->botRepository->getByUsers(...$usersNotified);

        $botSubscriptions = [];

        foreach ($notifiedBots as $bot) {
            foreach ($bot->BotSubscriptions as $botSubscription) {
                $botSubscriptions[] = $botSubscription;
            }
        }

        $this->webhookNotifier->notify(
            endpoint: self::CONVERSATION_MESSAGES_ENDPOINT,
            data: new ConversationMessageData($conversationMessage),
            botSubscriptions: $botSubscriptions,
        );
    }
}
