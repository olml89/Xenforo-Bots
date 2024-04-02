<?php declare(strict_types=1);

namespace olml89\XenforoBots\UseCase\XFConversationMessage;

use olml89\XenforoBots\Repository\BotSubscriptionRepository;
use olml89\XenforoBots\Service\WebhookNotifier;
use olml89\XenforoBots\XF\Entity\User as XFUser;
use XF\Entity\ConversationMessage as XFConversationMessage;

final class Notify
{
    private const CONVERSATION_MESSAGES_ENDPOINT = '/conversation-messages';

    public function __construct(
        private readonly BotSubscriptionRepository $subscriptionRepository,
        private readonly WebhookNotifier           $webhookNotifier,
    ) {}

    /**
     * @param XFUser[] $usersNotified
     */
    public function notify(XFConversationMessage $xFConversationMessage, array $usersNotified): void
    {
        $this->webhookNotifier->notify(
            endpoint: self::CONVERSATION_MESSAGES_ENDPOINT,
            subscriptions: $this->subscriptionRepository->getByUsers($usersNotified),
            data: new XFConversationMessageData($xFConversationMessage),
        );
    }
}
