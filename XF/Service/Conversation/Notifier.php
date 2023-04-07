<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Service\Conversation;

use olml89\XenforoSubscriptions\UseCase\XFConversationMessage\Notify as NotifyXFConversationMessage;
use olml89\XenforoSubscriptions\XF\Entity\User as XFUser;
use XF;
use XF\Entity\ConversationMessage as XFConversationMessage;

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

            /** @var NotifyXFConversationMessage $notifyXFConversationMessage */
            $notifyXFConversationMessage = XF::app()->get(NotifyXFConversationMessage::class);
            $notifyXFConversationMessage->notify($message, $usersNotified);
        }

        return $usersNotified;
    }
}
