<?php declare(strict_types=1);

namespace olml89\Subscriptions\XF\Service\Conversation;

use olml89\Subscriptions\UseCases\XFConversationMessage\NotifyXFConversationMessage;
use XF;
use XF\Entity\ConversationMessage as XFConversationMessage;
use XF\Entity\User as XFUser;

final class Notifier extends XFCP_Notifier
{
    /**
     * @return array<int, XFUser>
     */
    protected function _sendNotifications(
        $actionType,
        array $notifyUsers,
        XFConversationMessage $message = null,
        XFUser $sender = null
    ): array
    {
        $usersNotified = parent::_sendNotifications($actionType, $notifyUsers, $message, $sender);

        if (!is_null($message)) {

            /** @var NotifyConversationMessage $notifyConversation */
            $notifyConversation = XF::app()->get(NotifyXFConversationMessage::class);
            $notifyConversation->notify($message, $usersNotified);
        }

        return $usersNotified;
    }
}
