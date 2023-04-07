<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCase\XFConversationMessage;

use olml89\XenforoSubscriptions\Repository\SubscriptionRepository;
use olml89\XenforoSubscriptions\Service\WebhookNotifier;
use olml89\XenforoSubscriptions\XF\Entity\User as XFUser;
use XF\Entity\ConversationMessage as XFConversationMessage;

final class Notify
{
    private const CONVERSATION_MESSAGES_ENDPOINT = '/conversation-messages';

    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly WebhookNotifier $webhookNotifier,
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
