<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Service\Conversation;

use olml89\XenforoBots\UseCase\ConversationMessage\Notify as NotifyConversationMessage;
use XF;
use XF\Entity\ConversationMessage;
use XF\Entity\User;

final class Notifier extends XFCP_Notifier
{
    /**
     * @return User[]
     */
    protected function _sendNotifications(
        $actionType,
        array$notifyUsers,
        ConversationMessage $message = null,
        User $sender = null,
    ): array {
        $usersNotified = parent::_sendNotifications($actionType, $notifyUsers, $message, $sender);

        if (!is_null($message)) {
            /** @var NotifyConversationMessage $notifyConversationMessage */
            $notifyConversationMessage = XF::app()->get(NotifyConversationMessage::class);
            $notifyConversationMessage->notify($message, $usersNotified);
        }

        return $usersNotified;
    }
}
