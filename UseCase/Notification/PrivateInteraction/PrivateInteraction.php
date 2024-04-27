<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\Notification\PrivateInteraction;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\Entity\BotSubscriptionCollection;
use olml89\XenforoBots\Job\NotifyNotifiableJobData;
use olml89\XenforoBots\Job\NotifyPrivateInteractionJob;
use olml89\XenforoBots\UseCase\Notification\Data;
use olml89\XenforoBots\UseCase\Notification\NoActiveBotSubscriptionsException;
use olml89\XenforoBots\UseCase\Notification\Notifiable;
use olml89\XenforoBots\XF\Entity\ConversationMessage;
use olml89\XenforoBots\XF\Entity\ConversationRecipient;

final class PrivateInteraction implements Notifiable
{
    private const ENDPOINT = 'bots/%s/interactions/private';

    private readonly ConversationMessage $conversationMessage;
    private readonly BotSubscriptionCollection $botSubscriptions;
    private readonly NotifyNotifiableJobData $jobCreationData;
    private readonly Data $data;

    /**
     * @throws NoActiveBotSubscriptionsException
     */
    public function __construct(ConversationMessage $conversationMessage)
    {
        $this->conversationMessage = $conversationMessage;
        $this->botSubscriptions = $this->activeBotSubscriptions($this->conversationMessage);

        if ($this->botSubscriptions->isEmpty()) {
            throw new NoActiveBotSubscriptionsException($this->conversationMessage);
        }

        $this->jobCreationData = NotifyPrivateInteractionJob::getCreationData($this->conversationMessage);
        $this->data = Data::fromConversationMessage($this->conversationMessage);
    }

    private function activeBotSubscriptions(ConversationMessage $conversationMessage): BotSubscriptionCollection
    {
        /**
         * ConversationRecipients which are Bots
         *
         * @var ConversationRecipient[] $notifiableConversationRecipients
         */
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

        return $botSubscriptions->filter(
            fn (BotSubscription $botSubscription): bool => $botSubscription->is_active
        );
    }

    public function jobCreationData(): NotifyNotifiableJobData
    {
        return $this->jobCreationData;
    }

    public function entity(): ConversationMessage
    {
        return $this->conversationMessage;
    }

    public function endpoint(Bot $bot): string
    {
        return sprintf(self::ENDPOINT, $bot->bot_id);
    }

    public function botSubscriptions(): BotSubscriptionCollection
    {
        return $this->botSubscriptions;
    }

    public function data(): Data
    {
        return $this->data;
    }
}