<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\XFConversationMessage;

use olml89\Subscriptions\Repositories\SubscriptionRepository;
use olml89\Subscriptions\Services\WebhookNotifier\WebhookNotifier;
use XF\Entity\ConversationMessage as XFConversationMessage;
use XF\Entity\User as XFUser;

final class NotifyXFConversationMessage
{
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
            endpoint: 'conversation-message',
            subscriptions: $this->subscriptionRepository->getByUsers($usersNotified),
            data: new XFConversationMessageData($xFConversationMessage),
        );
    }
}
