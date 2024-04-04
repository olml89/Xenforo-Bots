<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\ConversationMessage;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscriptionCollection;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use olml89\XenforoBots\XF\Entity\ConversationRecipient;

final class Notify
{
    private const CONVERSATION_MESSAGES_ENDPOINT = '/conversation-messages';

    public function __construct(
        private readonly WebhookNotifier $webhookNotifier,
    ) {}

    public function notify(ConversationMessage $conversationMessage): void
    {
        /** @var ConversationRecipient[] $notifiableConversationRecipients */
        $notifiableConversationRecipients = $conversationMessage
            ->Conversation
            ->Recipients
            ->filter(
                function (ConversationRecipient $conversationRecipient) use ($conversationMessage): bool {
                    return !$conversationRecipient->User->same($conversationMessage->User)
                        && !is_null($conversationRecipient->User->Bot);
                }
            )
            ->toArray();

        $notifiableBots = array_map(
            fn (ConversationRecipient $conversationRecipient): Bot => $conversationRecipient->User->Bot,
            $notifiableConversationRecipients
        );

        $botSubscriptions = new BotSubscriptionCollection();

        foreach ($notifiableBots as $notifiableBot) {
            $botSubscriptions = $botSubscriptions->merge($notifiableBot->BotSubscriptions);
        }

        $this->webhookNotifier->notify(
            endpoint: self::CONVERSATION_MESSAGES_ENDPOINT,
            data: new ConversationMessageData($conversationMessage),
            botSubscriptions: $botSubscriptions,
        );
    }
}
