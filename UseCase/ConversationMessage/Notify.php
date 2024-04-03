<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\ConversationMessage;

use olml89\XenforoBots\Entity\ActiveBotSubscriptionCollection;
use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\User;
use XF\Entity\ConversationMessage;


final class Notify
{
    private const CONVERSATION_MESSAGES_ENDPOINT = '/conversation-messages';

    public function __construct(
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    /**
     * @param User[] $usersNotified
     */
    public function notify(ConversationMessage $conversationMessage, array $usersNotified): void
    {
        $notifiableBots = array_filter(
            array_map(
                fn (User $user): ?Bot => $user->Bot,
                $usersNotified
            )
        );

        $activeBotSubscriptions = new ActiveBotSubscriptionCollection(...$notifiableBots);

        $this->webhookNotifier->notify(
            endpoint: self::CONVERSATION_MESSAGES_ENDPOINT,
            data: new ConversationMessageData($conversationMessage),
            botSubscriptions: $activeBotSubscriptions,
        );
    }
}
