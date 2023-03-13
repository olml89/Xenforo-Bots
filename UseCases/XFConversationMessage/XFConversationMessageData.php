<?php declare(strict_types=1);

namespace olml89\Subscriptions\UseCases\XFConversationMessage;

use olml89\Subscriptions\UseCases\DataTransferObject;
use XF\Entity\ConversationMessage as XFConversationMessage;

final class XFConversationMessageData extends DataTransferObject
{
    public readonly int $message_id;
    public readonly int $conversation_id;
    public readonly int $sender_id;
    public readonly string $sender_name;
    public readonly int $message_date;
    public readonly string $message;

    public function __construct(XFConversationMessage $xFConversationMessage)
    {
        $this->message_id = $xFConversationMessage->message_id;
        $this->conversation_id = $xFConversationMessage->conversation_id;
        $this->sender_id = $xFConversationMessage->user_id;
        $this->sender_name = $xFConversationMessage->username;
        $this->message_date = $xFConversationMessage->message_date;
        $this->message = $xFConversationMessage->message;
    }
}