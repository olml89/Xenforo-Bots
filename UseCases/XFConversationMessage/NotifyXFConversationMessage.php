<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\UseCases\XFConversationMessage;

use olml89\XenforoSubscriptions\Repositories\SubscriptionRepository;
use olml89\XenforoSubscriptions\Services\WebhookNotifier\WebhookNotifier;
use XF\Entity\ConversationMessage as XFConversationMessage;
use XF\Entity\User as XFUser;

final class NotifyXFConversationMessage
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
